<a @class(['active' => $request->orderBy == $orderBy]) href="{{ $request->reversedSortingUrl . '&orderBy=' . $orderBy }}">
    <span>{{ __($column['name']) }}</span>
    <span class="material-symbols-outlined">expand_all</span>
</a>
