<h3 class="main-title">{{ __('Similar records') }}</h3>

@if (count($similarRecords))
    <div class="similar-records__list">
        @foreach ($similarRecords as $kvpp)
            <div class="similar-records__list-item">
                <x-different.arrowed-link href="{{ route('kvpp.edit', $kvpp->id) }}">{{ __('View') }}</x-different.arrowed-link>

                <div class="similar-records__list-text">
                    <span>{{ __('ID') }}: {{ $kvpp->id }}</span>
                    <span>{{ __('Form') }}: {{ $kvpp->form->name }}</span>
                    <span>{{ __('Dosage') }}: {{ $kvpp->dosage }}</span>
                    <span>{{ __('Pack') }}: {{ $kvpp->pack }}</span>
                    <span>{{ __('Country') }}: {{ $kvpp->country->name }}</span>
                    <span>{{ __('MAH') }}: {{ $kvpp->marketingAuthorizationHolder->name }}</span>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="similar-records__empty-text">{{ __('No similar records found') }}!</p>
@endif
