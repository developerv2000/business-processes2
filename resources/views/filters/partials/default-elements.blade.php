<x-forms.date-range-input.request-based-input
    label="Date of creation"
    name="created_at"
/>

<x-forms.date-range-input.request-based-input
    label="Update date"
    name="updated_at"
/>

@if ($includeIdInput)
    <x-forms.input.request-based-input
        label="ID"
        name="id"
        type="number"
    />
@endif

@include('filters.partials.pagination-limit')
