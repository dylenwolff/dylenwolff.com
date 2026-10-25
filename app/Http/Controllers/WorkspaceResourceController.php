<?php

namespace App\Http\Controllers;

use App\Support\Countries;
use App\Support\WorkspaceResources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkspaceResourceController extends Controller
{
    public function index(Request $request, string $section): View|RedirectResponse
    {
        $resource = WorkspaceResources::get($section);
        $model = $resource['model'];

        if ($resource['singleton'] ?? false) {
            $record = $model::query()->first();
            return $record ? redirect()->route('workspace.resources.edit', [$section, $record]) : redirect()->route('workspace.resources.create', $section);
        }

        $query = $model::query();
        $search = trim((string) $request->query('search'));
        if ($search !== '') {
            $fields = array_values(array_filter((new $model)->getFillable(), fn ($field) => ! str_ends_with($field, '_id') && ! in_array($field, ['gallery', 'image_path', 'file_path', 'profile_photo'], true)));
            $query->where(function ($query) use ($fields, $search) {
                foreach ($fields as $field) $query->orWhere($field, 'like', '%'.$search.'%');
            });
        }

        if ($country = $request->query('country')) {
            $section === 'organizations'
                ? $query->where('country_code', $country)
                : $query->whereHas('organization', fn ($query) => $query->where('country_code', $country));
        }

        return view('workspace.resources.index', [
            'section' => $section, 'resource' => $resource, 'records' => $query->latest()->paginate(20)->withQueryString(),
            'countries' => in_array($section, ['organizations', 'leads', 'opportunities'], true) ? Countries::options() : [],
        ]);
    }

    public function create(string $section): View
    {
        $resource = WorkspaceResources::get($section);
        abort_if(($resource['create'] ?? true) === false, 404);
        return $this->form($section, $resource, new $resource['model']);
    }

    public function store(Request $request, string $section): RedirectResponse
    {
        $resource = WorkspaceResources::get($section);
        abort_if(($resource['create'] ?? true) === false, 404);
        $model = new $resource['model'];
        $model->fill($this->payload($request, $section, $model));
        $model->save();
        return redirect()->route('workspace.manage', $section)->with('success', $resource['singular'].' created.');
    }

    public function edit(string $section, int $record): View
    {
        $resource = WorkspaceResources::get($section);
        return $this->form($section, $resource, $resource['model']::query()->findOrFail($record));
    }

    public function update(Request $request, string $section, int $record): RedirectResponse
    {
        $resource = WorkspaceResources::get($section);
        $model = $resource['model']::query()->findOrFail($record);
        $model->fill($this->payload($request, $section, $model));
        $model->save();
        return redirect()->route('workspace.manage', $section)->with('success', $resource['singular'].' updated.');
    }

    public function destroy(string $section, int $record): RedirectResponse
    {
        $resource = WorkspaceResources::get($section);
        abort_if($resource['singleton'] ?? false, 403);
        $resource['model']::query()->findOrFail($record)->delete();
        return redirect()->route('workspace.manage', $section)->with('success', $resource['singular'].' deleted.');
    }

    private function form(string $section, array $resource, Model $record): View
    {
        $columns = collect(Schema::getColumns($record->getTable()))->keyBy('name');
        $fields = collect($record->getFillable())->map(function ($name) use ($columns, $section, $resource) {
            $column = $columns->get($name, []);
            return [
                'name' => $name,
                'label' => WorkspaceResources::label($name),
                'type' => WorkspaceResources::fieldType($name, $column['type_name'] ?? 'varchar'),
                'options' => WorkspaceResources::options($section, $name),
                'required' => ($column['nullable'] ?? true) === false && ($column['default'] ?? null) === null,
                'readonly' => in_array($name, $resource['readonly'] ?? [], true),
            ];
        });
        return view('workspace.resources.form', compact('section', 'resource', 'record', 'fields'));
    }

    private function payload(Request $request, string $section, Model $model): array
    {
        $columns = collect(Schema::getColumns($model->getTable()))->keyBy('name');
        $data = [];
        foreach ($model->getFillable() as $field) {
            $column = $columns->get($field, []);
            $type = WorkspaceResources::fieldType($field, $column['type_name'] ?? 'varchar');
            if (in_array($field, WorkspaceResources::get($section)['readonly'] ?? [], true)) continue;
            if ($type === 'checkbox') { $data[$field] = $request->boolean($field); continue; }
            if (in_array($type, ['image', 'file'], true)) {
                if ($request->hasFile($field)) {
                    $directory = match (true) {
                        $type === 'file' => 'client-documents',
                        $field === 'profile_photo' => 'profile',
                        $section === 'projects' => 'projects/covers',
                        default => 'awards',
                    };
                    $data[$field] = $request->file($field)->store($directory, $type === 'file' ? 'local' : 'public');
                }
                continue;
            }
            if ($type === 'images') {
                if ($request->hasFile($field)) $data[$field] = collect($request->file($field))->map(fn ($file) => $file->store('projects/gallery', 'public'))->all();
                continue;
            }
            $value = $request->input($field);
            if (($value === null || $value === '') && ! $model->exists && ($column['default'] ?? null) !== null) continue;
            if ($type === 'tags') $value = collect(preg_split('/[,\r\n]+/', (string) $value))->map(fn ($item) => trim($item))->filter()->values()->all();
            $data[$field] = $value === '' ? null : $value;
        }
        return $data;
    }
}
