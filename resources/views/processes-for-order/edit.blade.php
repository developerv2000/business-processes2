@extends('layouts.app', ['page' => 'processes-for-order-edit'])

@section('main')
    <div class="pre-content styled-box">
        @include('layouts.breadcrumbs', [
            'crumbs' => [__('For order'), __('Edit'), '# ' . $instance->id],
            'fullScreen' => false,
        ])

        <div class="pre-content__actions">
            <x-different.button style="action" icon="add" type="submit" form="edit-form">{{ __('Update') }}</x-different.button>
        </div>
    </div>

    <x-forms.template.edit-template action="{{ route('processes_for_order.update', $instance->id) }}">
        <div class="form__section">
            <h1 class="form__title main-title">{{ __('About product') }}</h1>

            <table class="table secondary-table">
                <tbody>
                    <tr>
                        <td>{{ __('VPS Brand Eng') }}:</td>
                        <td>{{ $instance->trademark_en }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('VPS Brand Rus') }}:</td>
                        <td>{{ $instance->trademark_ru }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('Manufacturer') }}:</td>
                        <td>{{ $instance->manufacturer->name }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('Form') }}:</td>
                        <td>{{ $instance->product->form->name }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('Pack') }}:</td>
                        <td>{{ $instance->product->pack }}</td>
                    </tr>

                    <tr>
                        <td>{{ __('Country') }}:</td>
                        <td>{{ $instance->searchCountry->name }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="form__section">
            <x-forms.input.instance-edit-input
                label="Brand name ENG"
                name="fixed_trademark_en_for_order"
                :instance="$instance"
                required />

            <x-forms.input.instance-edit-input
                label="Brand name RUS"
                name="fixed_trademark_ru_for_order"
                :instance="$instance"
                required />
        </div>
    </x-forms.template.edit-template>
@endsection
