<div class="sortable-columns">
    @foreach ($columns as $column)
        <div class="sortable-columns__item" data-column-name="{{ $column['name'] }}">
            <p class="sortable-columns__title">{{ __($column['name']) }}</p>

            <x-forms.groups.default-group class="form-group--horizontal" label="{{ __('Visible') }}">
                <input class="switch" type="checkbox" value="1" @checked($column['visible'])>
            </x-forms.groups.default-group>

            <x-forms.groups.default-group class="form-group--horizontal" label="{{ __('Width in pixels') }}">
                <input class="input sortable-columns__width-input" type="number" value="{{ $column['width'] }}" min="1" max="2000">
            </x-forms.groups.default-group>

            <div class="sortable-columns__width" style="width: {{ $column['width'] }}px"></div>
        </div>
    @endforeach
</div>
