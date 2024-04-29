<h3 class="main-title">{{ __('Similar records') }}</h3>

@if (count($similarRecords))
    <div class="similar-records__list">
        @foreach ($similarRecords as $product)
            <div class="similar-records__list-item">
                <x-different.arrowed-link href="{{ route('products.edit', $product->id) }}">{{ __('View') }}</x-different.arrowed-link>

                <div class="similar-records__list-text">
                    <span>{{ __('ID') }}: {{ $product->id }}</span>
                    <span>{{ __('Form') }}: {{ $product->form->name }}</span>
                    <span>{{ __('Dosage') }}: {{ $product->dose }}</span>
                    <span>{{ __('Pack') }}: {{ $product->pack }}</span>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="similar-records__empty-text">{{ __('No similar records found') }}</p>
@endif
