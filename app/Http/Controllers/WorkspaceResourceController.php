<?php

namespace App\Http\Controllers;

use App\Support\Countries;
use App\Support\WorkspaceResources;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
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
        if ($section === 'leads') {
            $query->with(['organization', 'contact']);
        }
        if ($section === 'contacts') {
            $query->with('organization');
        }
        if ($section === 'crm-tasks') {
            $query->with(['lead.organization', 'contact', 'opportunity.organization']);
        }
        $search = trim((string) $request->query('search'));
        if ($search !== '') {
            $fields = array_values(array_filter((new $model)->getFillable(), fn ($field) => ! str_ends_with($field, '_id') && ! in_array($field, ['gallery', 'image_path', 'file_path', 'profile_photo'], true)));
            $query->where(function ($query) use ($fields, $search) {
                foreach ($fields as $field) {
                    $query->orWhere($field, 'like', '%'.$search.'%');
                }
            });
        }

        if ($country = $request->query('country')) {
            $section === 'organizations'
                ? $query->where('country_code', $country)
                : $query->whereHas('organization', fn ($query) => $query->where('country_code', $country));
        }

        $statuses = in_array($section, ['contact-messages', 'outreach-emails'], true)
            ? WorkspaceResources::options($section, 'status')
            : [];
        if ($statuses) {
            $status = $request->query('status');
            if ($status === 'all') {
                // Intentionally show every message.
            } elseif ($status && array_key_exists($status, $statuses)) {
                $query->where('status', $status);
            } elseif ($section === 'contact-messages') {
                $query->where('status', '!=', 'spam');
            }
        }

        $this->applyOrdering($query, $section);

        return view('workspace.resources.index', [
            'section' => $section, 'resource' => $resource, 'records' => $query->paginate(20)->withQueryString(),
            'countries' => in_array($section, ['organizations', 'leads', 'opportunities'], true) ? Countries::options() : [],
            'statuses' => $statuses,
            'statusDefaultLabel' => $section === 'contact-messages' ? 'Inbox' : 'All statuses',
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

    public function show(string $section, int $record): View
    {
        $resource = WorkspaceResources::get($section);
        $model = $resource['model']::query()->findOrFail($record);
        $relationships = collect(['organization', 'contact', 'lead', 'opportunity', 'clientProject'])
            ->filter(fn (string $relationship): bool => method_exists($model, $relationship))
            ->values()
            ->all();

        if ($relationships !== []) {
            $model->load($relationships);
        }

        if ($section === 'client-projects') {
            $model->loadCount(['invoices', 'maintenancePlans', 'supportTickets', 'documents']);
        }

        $columns = collect(Schema::getColumns($model->getTable()))->keyBy('name');
        $fields = collect($model->getFillable())->map(function (string $name) use ($columns, $section): array {
            $column = $columns->get($name, []);

            return [
                'name' => $name,
                'label' => WorkspaceResources::label($name),
                'type' => WorkspaceResources::fieldType($name, $column['type_name'] ?? 'varchar'),
                'options' => WorkspaceResources::options($section, $name),
            ];
        });

        return view('workspace.resources.show', compact('section', 'resource', 'model', 'fields'));
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
            if (in_array($field, WorkspaceResources::get($section)['readonly'] ?? [], true)) {
                continue;
            }
            if ($type === 'checkbox') {
                $data[$field] = $request->boolean($field);

                continue;
            }
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
                if ($request->hasFile($field)) {
                    $data[$field] = collect($request->file($field))->map(fn ($file) => $file->store('projects/gallery', 'public'))->all();
                }

                continue;
            }
            $value = $request->input($field);
            if (($value === null || $value === '') && ! $model->exists && ($column['default'] ?? null) !== null) {
                continue;
            }
            if ($type === 'tags') {
                $value = collect(preg_split('/[,\r\n]+/', (string) $value))->map(fn ($item) => trim($item))->filter()->values()->all();
            }
            $data[$field] = $value === '' ? null : $value;
        }

        return $data;
    }

    private function applyOrdering($query, string $section): void
    {
        match ($section) {
            'crm-tasks' => $query
                ->orderByRaw("case status when 'in_progress' then 1 when 'todo' then 2 when 'completed' then 3 when 'done' then 3 when 'cancelled' then 4 else 5 end")
                ->orderByRaw("case priority when 'urgent' then 1 when 'high' then 2 when 'normal' then 3 when 'low' then 4 else 5 end")
                ->orderByRaw('due_at IS NULL')->orderBy('due_at')->latest('updated_at'),
            'activities' => $query->latest('occurred_at')->latest('id'),
            'client-projects' => $query
                ->orderByRaw("case status when 'active' then 1 when 'planned' then 2 when 'on_hold' then 3 when 'completed' then 4 when 'cancelled' then 5 else 6 end")
                ->orderByRaw('deadline IS NULL')->orderBy('deadline')->latest('updated_at'),
            'leads' => $query
                ->orderByRaw("case status when 'qualified' then 1 when 'contacted' then 2 when 'new' then 3 when 'converted' then 4 when 'unqualified' then 5 else 6 end")
                ->orderByRaw('next_follow_up_at IS NULL')->orderBy('next_follow_up_at')->latest('updated_at'),
            'opportunities' => $query
                ->orderByRaw("case stage when 'negotiation' then 1 when 'proposal' then 2 when 'qualification' then 3 when 'prospecting' then 4 when 'won' then 5 when 'lost' then 6 else 7 end")
                ->orderByRaw('expected_close_date IS NULL')->orderBy('expected_close_date')->orderByDesc('amount'),
            'outreach-emails' => $query
                ->orderByRaw("case status when 'approved' then 1 when 'ready' then 2 when 'draft' then 3 when 'sending' then 4 when 'sent' then 5 when 'replied' then 6 when 'bounced' then 7 when 'do_not_contact' then 8 else 9 end")
                ->orderByRaw('follow_up_at IS NULL')->orderBy('follow_up_at')->latest('updated_at'),
            'contact-messages' => $query
                ->orderByRaw("case status when 'new' then 1 when 'replied' then 2 when 'archived' then 3 when 'spam' then 4 else 5 end")
                ->latest('created_at'),
            'support-tickets' => $query
                ->orderByRaw("case status when 'open' then 1 when 'in_progress' then 2 when 'waiting' then 3 when 'resolved' then 4 when 'closed' then 5 else 6 end")
                ->orderByRaw("case priority when 'urgent' then 1 when 'high' then 2 when 'normal' then 3 when 'low' then 4 else 5 end")
                ->orderByRaw('due_at IS NULL')->orderBy('due_at'),
            'invoices' => $query
                ->orderByRaw("case status when 'overdue' then 1 when 'part_paid' then 2 when 'sent' then 3 when 'draft' then 4 when 'paid' then 5 when 'void' then 6 else 7 end")
                ->orderByRaw('due_at IS NULL')->orderBy('due_at')->latest('updated_at'),
            'quotes' => $query
                ->orderByRaw("case status when 'sent' then 1 when 'draft' then 2 when 'accepted' then 3 when 'declined' then 4 when 'expired' then 5 else 6 end")
                ->orderByRaw('valid_until IS NULL')->orderBy('valid_until')->latest('updated_at'),
            'maintenance-plans' => $query
                ->orderByRaw("case status when 'active' then 1 when 'paused' then 2 when 'ended' then 3 when 'cancelled' then 4 else 5 end")
                ->orderByRaw('next_billing_date IS NULL')->orderBy('next_billing_date'),
            'projects', 'services' => $query->orderBy('sort_order')->latest('updated_at'),
            'awards' => $query->orderByDesc('is_featured')->orderByDesc('awarded_at')->latest('id'),
            'qualifications' => $query->orderByDesc('is_published')->orderByDesc('end_year')->latest('id'),
            'client-documents' => $query->latest('updated_at'),
            'organizations' => $query
                ->orderByRaw("case status when 'client' then 1 when 'prospect' then 2 when 'partner' then 3 when 'inactive' then 4 else 5 end")
                ->orderBy('name'),
            'contacts' => $query
                ->orderByRaw("case status when 'active' then 1 when 'inactive' then 2 else 3 end")
                ->orderBy('first_name')->orderBy('last_name'),
            default => $query->latest('updated_at'),
        };
    }
}
