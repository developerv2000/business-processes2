{{-- Pagination height plays role on calculating Table wrapper`s height via CSS calc function --}}
{{-- That`s why manual validation is required --}}

@if (!$records->hasPages())
    <style>
        .table-wrapper {
            --table-pagination-height: 0px;
        }
    </style>
@endif
