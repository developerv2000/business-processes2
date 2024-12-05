<div class="invoices-create-goods__products-list styled-box">
    <h2 class="main-title invoices-create-goods__products-list-title">Products list</h2>

    @php
        $totalLoopIndex = 0;
    @endphp

    @foreach ($orders as $order)
        <h3 class="invoices-create-goods__products-list-order-title">{{ $order->label }}</h3>

        <table class="main-table">
            <thead>
                <th width="40">
                    <span class="th__select-all unselectable material-symbols-outlined">priority</span>
                </th>

                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
            </thead>

            <tbody>
                @foreach ($order->invoice_products as $product)
                    @php
                        $totalLoopIndex++;
                    @endphp

                    <tr>
                        <td>
                            <input class="checkbox td__checkbox" type="checkbox" name="products[{{ $totalLoopIndex }}][id]" value="{{ $product->id }}" checked>
                        </td>

                        <td>
                            {{ $product->process->fixed_trademark_en_for_order }}
                        </td>

                        <td>
                            <input class="input" type="number" name="products[{{ $totalLoopIndex }}][quantity]" value="{{ $product->quantity }}" required>
                        </td>

                        <td>
                            <input class="input" type="number" name="products[{{ $totalLoopIndex }}][price]" step="0.01" value="{{ $product->invoice_price ?: $product->price }}" required>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>

{{-- Other payments --}}
<div class="invoices-create__other-payments styled-box">
    <h2 class="invoices-create__other-payments-title main-title">Other payments list</h2>

    <div class="invoices-create__other-payments-list"></div>

    <x-different.button type="button" class="invoices-create__add-other-payments-btn" style="action" icon="add" type="button">
        {{ __('Add other payments') }}
    </x-different.button>
</div>
