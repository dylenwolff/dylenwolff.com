@extends('layouts.workspace')

@section('title', ($record->exists ? 'Edit ' : 'Add ').$resource['singular'])
@section('heading', $resource['label'])

@section('content')
<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex items-center gap-3"><a href="{{ route('workspace.manage', $section) }}" class="btn btn-square btn-ghost"><i data-lucide="arrow-left" class="size-5"></i></a><div><h2 class="text-2xl font-black">{{ $record->exists ? 'Edit' : 'Add' }} {{ strtolower($resource['singular']) }}</h2><p class="text-sm text-base-content/55">{{ $record->exists ? 'Update the record below.' : 'Enter the details below.' }}</p></div></div>
    @if($errors->any())<div class="alert alert-error"><i data-lucide="circle-alert" class="size-5"></i><div><strong>Please check the form.</strong><ul class="mt-1 list-inside list-disc text-sm">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
    <form method="POST" enctype="multipart/form-data" action="{{ $record->exists ? route('workspace.resources.update', [$section, $record]) : route('workspace.resources.store', $section) }}" class="card border border-base-300 bg-base-100 shadow-sm">
        @csrf @if($record->exists) @method('PUT') @endif
        <div class="card-body grid gap-5 md:grid-cols-2">
            @foreach($fields as $field)
                @php($value = old($field['name'], $record->{$field['name']}))
                <label class="form-control {{ in_array($field['type'], ['textarea','image','images','file','tags'], true) ? 'md:col-span-2' : '' }}">
                    <span class="label-text mb-1.5 font-bold">{{ $field['label'] }} @if($field['required'])<span class="text-error">*</span>@endif</span>
                    @if($field['options'])
                        <select name="{{ $field['name'] }}" class="select select-bordered w-full" @disabled($field['readonly']) @required($field['required'])><option value="">Select an option</option>@foreach($field['options'] as $key => $label)<option value="{{ $key }}" @selected((string)$value === (string)$key)>{{ $label }}</option>@endforeach</select>
                    @elseif($field['type'] === 'textarea')
                        <textarea name="{{ $field['name'] }}" rows="{{ in_array($field['name'], ['about_body','message','body'], true) ? 12 : 5 }}" class="textarea textarea-bordered w-full" @readonly($field['readonly']) @required($field['required'])>{{ $value }}</textarea>
                    @elseif($field['type'] === 'checkbox')
                        <span class="flex min-h-12 items-center gap-3 rounded-box border border-base-300 px-4"><input type="checkbox" name="{{ $field['name'] }}" value="1" class="toggle toggle-primary" @checked((bool)$value)><span>Enabled</span></span>
                    @elseif(in_array($field['type'], ['image','images','file'], true))
                        @if($value && $field['type'] === 'image')<img src="{{ asset('storage/'.$value) }}" class="mb-3 max-h-52 rounded-box border border-base-300 object-contain" alt="Current upload">@endif
                        <input type="file" name="{{ $field['name'] }}{{ $field['type'] === 'images' ? '[]' : '' }}" class="file-input file-input-bordered w-full" @if(in_array($field['type'], ['image','images'], true)) accept="image/*" @endif @if($field['type'] === 'images') multiple @endif>
                    @elseif($field['type'] === 'tags')
                        <input name="{{ $field['name'] }}" value="{{ is_array($value) ? implode(', ', $value) : $value }}" class="input input-bordered w-full" placeholder="Separate items with commas">
                    @else
                        <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" value="{{ $field['type'] === 'datetime-local' && $value ? \Carbon\Carbon::parse($value)->format('Y-m-d\TH:i') : $value }}" class="input input-bordered w-full" @readonly($field['readonly']) @required($field['required']) @if($field['type'] === 'number') step="any" @endif>
                    @endif
                </label>
            @endforeach
        </div>
        <div class="flex flex-wrap gap-2 border-t border-base-300 p-5"><button class="btn btn-primary"><i data-lucide="save" class="size-4"></i>{{ $record->exists ? 'Save changes' : 'Create' }}</button><a href="{{ route('workspace.manage', $section) }}" class="btn btn-ghost">Cancel</a></div>
    </form>
</div>
@endsection
