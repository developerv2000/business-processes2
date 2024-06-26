@switch($instance->type)
    @case('App\Notifications\ProcessOnContractStage')
        <div>{{ __('Status of process') }}
            <a class="td__link" href="{{ route('processes.index', ['id' => $instance->data['process_id']]) }}">#{{ $instance->data['process_id'] }}</a>
            {{ __('has been updated to') }} {{ $instance->data['status_name'] }}
        </div>
        @break

    @default
        Undefined
@endswitch
