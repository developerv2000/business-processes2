<x-forms.form-group label="{{ __('Items per page') }}">
    <select
        name="paginationLimit"
        class='singular-selectize'
    >
        @foreach ($paginationLimits as $option)
            <option value="{{ $option }}" @selected($option == request()->input('paginationLimit'))>{{ $option }}</option>
        @endforeach
    </select>
</x-forms.form-group>
