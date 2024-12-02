@include('tables.style-validations')

<div class="table-wrapper thin-scrollbar">
    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                <th width="48">@include('tables.components.th.edit')</th>
                <th width="44">ID</th>
                <th width="80">Descr</th>
                <th width="120">PO №</th>
                <th width="100">PO date</th>
                <th width="70">Market</th>
                <th width="100">Invoice</th>
                <th width="100">Product</th>
                <th width="110">Payment type</th>
                <th width="140">Manufacturer</th>
                <th width="110">Payer</th>
                <th width="80">Invoice</th>
                <th width="100">Inv date</th>
                <th width="100">Quantity</th>
                <th width="100">Price</th>
                <th width="80">Currency</th>
                <th width="100">Sum price</th>
                <th width="70">Terms</th>
                <th width="100">Due amount</th>
                <th width="100">Prepayment</th>
                <th width="100">Send to pay</th>
                <th width="100">Paid</th>
                <th width="100">Pay date</th>
                <th width="100">Differ</th>
                <th width="100">Status</th>
                <th width="120">Payment refer</th>
            </tr>
        </thead> {{-- Head end --}}

        {{-- Body Start --}}
        <tbody>
            @foreach ($records as $instance)
                <tr>
                    <td>
                        @include('tables.components.td.edit-button', ['href' => route('invoice-items.edit', $instance->id)])
                    </td>

                    <td>{{ $instance->id }}</td>
                    <td>{{ $instance->category->name }}</td>
                    <td>{{ $instance->invoice->order->purchase_order_name }}</td>
                    <td>{{ $instance->invoice->order->purchase_order_date->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->invoice->order->country->name }}</td>

                    <td>
                        <a href="{{ route('invoices.index', ['id[]' => $instance->invoice->id]) }}" class="td__link">
                            {{ $instance->invoice->name }}
                        </a>
                    </td>

                    <td>{{ $instance->orderProduct->process->fixed_trademark_ru_for_order }}</td>
                    <td>{{ $instance->invoice->paymentType->name }}</td>
                    <td>{{ $instance->invoice->order->manufacturer->name }}</td>
                    <td>{{ $instance->invoice->payer->name }}</td>
                    <td>{{ $instance->invoice->name }}</td>
                    <td>{{ $instance->invoice->date->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->quantity }}</td>
                    <td>{{ $instance->orderProduct->invoice_price }}</td>
                    <td>{{ $instance->invoice->order->currency->name }}</td>
                    <td>{{ $instance->total_price }}</td>
                    <td>{{ $instance->terms }} %</td>
                    <td>{{ $instance->payment_due }}</td>
                    <td>{{ $instance->prepayment_amount }}</td>
                    <td>{{ $instance->invoice->order->sent_for_payment_date?->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->amount_paid }}</td>
                    <td>{{ $instance->invoice->order->payment_date?->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->payment_difference }}</td>
                    <td>{{ $instance->invoice->order->status }}</td>
                    <td>{{ $instance->invoice->order->group_name }}</td>
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>
