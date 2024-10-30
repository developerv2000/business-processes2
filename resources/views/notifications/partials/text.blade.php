@switch($instance->type)
    @case('App\Notifications\ProcessOnContractStage')
        <div>
            {{ __('Status of process') }}
            <a class="td__link" href="{{ route('processes.index', ['id[]' => $instance->data['process_id']]) }}">#{{ $instance->data['process_id'] }}</a>
            {{ __('has been updated to') }} {{ $instance->data['status_name'] }}
        </div>
    @break

    @case('App\Notifications\ProcessMarkedAsReadyForOrder')
        <div>
            <strong>{{ __('New product for order has been received:') }}</strong><br>
            {{ __('Country') }}: {{ $instance->data['country'] }}<br>
            {{ __('Manufacturer') }}: {{ $instance->data['manufacturer'] }}<br>
            {{ __('Brand Eng') }}: {{ $instance->data['trademark_en'] }}<br>
            {{ __('Brand Rus') }}: {{ $instance->data['trademark_ru'] }}<br>
            {{ __('MAH') }}: {{ $instance->data['marketing_authorization_holder'] }}<br>
            {{ __('Form') }}: {{ $instance->data['form'] }}<br>
            {{ __('Pack') }}: {{ $instance->data['pack'] }}
        </div>
    @break

    @default
        Undefined
@endswitch
