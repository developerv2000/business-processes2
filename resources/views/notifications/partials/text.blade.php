@switch($instance->type)
    @case('App\Notifications\ProcessOnContractStage')
        <div>
            {{ __('Status of process') }}
            <a class="td__link" href="{{ route('processes.index', ['id[]' => $instance->data['process_id']]) }}">#{{ $instance->data['process_id'] }}</a>
            {{ __('has been updated to') }} {{ $instance->data['status_name'] }}
        </div>
    @break

    @case('App\Notifications\ApplicationReceivedNotification')
        <div>
            {{ __('Received new application') }}
            <a class="td__link" href="{{ route('applications.index', ['id[]' => $instance->data['application_id']]) }}">#{{ $instance->data['application_id'] }}</a>.<br>
            {{ __('Country') }}: {{ $instance->data['country'] }}<br>
            {{ __('Manufacturer') }}: {{ $instance->data['manufacturer'] }}<br>
            {{ __('Brand Eng') }}: {{ $instance->data['trademark_en'] }}<br>
            {{ __('Brand Rus') }}: {{ $instance->data['trademark_ru'] }}<br>
            {{ __('MAH') }}: {{ $instance->data['marketing_authorization_holder'] }}
        </div>
    @break

    @default
        Undefined
@endswitch
