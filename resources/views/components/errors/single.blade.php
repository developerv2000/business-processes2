@props(['name'])

@if ($errors->has($name))
    <div class="errors">
        <p class="errors__title">{{ __('Error') }}! {{ __('Please correct the errors and try again') }}.</p>

        <ul class="errors__list">
            @foreach ($errors->get($name) as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
