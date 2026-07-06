@extends('layouts.app', [
    'title' => ($record ? 'Edit ' : 'Tambah ') . $config['singular'],
    'subtitle' => 'Lengkapi data ' . strtolower($config['singular']) . ' sesuai kebutuhan sistem.',
])

@section('content')
    <div class="mb-5">
        <a href="{{ route('masters.crud.index', $resource) }}" class="btn-secondary">Kembali ke {{ $config['label'] }}</a>
    </div>

    <section class="panel max-w-5xl">
        <form method="POST" action="{{ $record ? route('masters.crud.update', [$resource, $record->id]) : route('masters.crud.store', $resource) }}" class="grid gap-5 md:grid-cols-2">
            @csrf
            @if ($record)
                @method('PUT')
            @endif

            @foreach ($config['fields'] as $field)
                @php
                    $name = $field['name'];
                    $type = $field['type'] ?? 'text';
                    $value = old($name, $record ? data_get($record, $name) : null);
                    if ($value instanceof \Carbon\CarbonInterface) {
                        $value = $value->toDateString();
                    }
                @endphp

                <label class="form-label {{ $type === 'textarea' ? 'md:col-span-2' : '' }}">
                    {{ $field['label'] }}

                    @if ($type === 'textarea')
                        <textarea name="{{ $name }}" rows="4" class="form-input">{{ $value }}</textarea>
                    @elseif ($type === 'select')
                        <select name="{{ $name }}" class="form-input">
                            <option value="">- Pilih -</option>
                            @foreach ($options[$field['options']] ?? [] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @elseif ($type === 'select_static')
                        <select name="{{ $name }}" class="form-input">
                            <option value="">- Pilih -</option>
                            @foreach ($field['choices'] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @elseif ($type === 'checkbox')
                        <input type="hidden" name="{{ $name }}" value="0">
                        <span class="mt-1 inline-flex items-center gap-2 rounded-md border border-stone-300 px-3 py-2">
                            <input type="checkbox" name="{{ $name }}" value="1" class="h-4 w-4 rounded border-stone-300 text-ma-red" @checked((bool) ($value ?? true))>
                            <span class="text-sm font-normal text-stone-700">Ya, aktif</span>
                        </span>
                    @else
                        <input
                            name="{{ $name }}"
                            type="{{ $type === 'number_decimal' ? 'number' : $type }}"
                            value="{{ $value }}"
                            class="form-input"
                            @if ($type === 'number_decimal') step="0.001" @endif
                            @if ($type === 'number') step="1" @endif
                        >
                    @endif

                    @error($name)
                        <span class="text-xs font-medium text-red-700">{{ $message }}</span>
                    @enderror
                </label>
            @endforeach

            <div class="flex flex-wrap gap-3 border-t border-stone-100 pt-5 md:col-span-2">
                <button class="btn-primary" type="submit">Simpan</button>
                <a href="{{ route('masters.crud.index', $resource) }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </section>
@endsection
