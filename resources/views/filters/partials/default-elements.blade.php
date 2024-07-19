<x-forms.date-range-input.request-based-input
    label="Date of creation"
    name="created_at" />

<x-forms.date-range-input.request-based-input
    label="Update date"
    name="updated_at" />

@if ($includeIdInput)
    <x-forms.multiple-select.request-based-select
        label="ID"
        name="id[]"
        :taggable="true"
        :options="request()->input('id', [])" />
@endif

@include('filters.partials.pagination-limit')
