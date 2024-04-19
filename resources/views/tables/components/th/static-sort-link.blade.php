<a @class(['active' => $request->orderBy == $orderBy]) href="{{ $request->reversedSortingUrl . '&orderBy=' . $orderBy }}">
    <span>{{ __($text) }}</span>
    <span class="material-symbols-outlined">expand_all</span>
</a>
