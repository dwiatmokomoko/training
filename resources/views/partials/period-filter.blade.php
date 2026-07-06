<form method="GET" class="flex flex-wrap items-center gap-2">
    <select name="period" class="form-input min-w-64">
        @foreach ($periods as $item)
            <option value="{{ $item->id }}" @selected($period?->id === $item->id)>{{ $item->name }}</option>
        @endforeach
    </select>
    <button class="btn-primary">Tampilkan</button>
</form>
