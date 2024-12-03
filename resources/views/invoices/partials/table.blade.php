@include('tables.style-validations')

<div class="table-wrapper thin-scrollbar">
    <table class="table main-table">
        {{-- Head start --}}
        <thead>
            <tr>
                <th width="48">@include('tables.components.th.edit')</th>
                <th width="44">ID</th>
                <th width="80">Descr</th>
                <th width="80">Invoice</th>
                <th width="100">Date</th>
                <th width="110">Payment type</th>
                <th width="80">Items</th>
                <th width="120">PO №</th>
                <th width="100">PO date</th>
                <th width="140">Manufacturer</th>
                <th width="110">Payer</th>
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
                        @include('tables.components.td.edit-button', ['href' => route('invoices.edit', $instance->id)])
                    </td>

                    <td>{{ $instance->id }}</td>
                    <td>{{ $instance->category->name }}</td>
                    <td>{{ $instance->name }}</td>
                    <td>{{ $instance->date->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->paymentType->name }}</td>

                    <td>
                        <a href="{{ route('invoice-items.index', ['invoice_id' => $instance->id]) }}" class="td__link">
                            {{ $instance->items_count }} items
                        </a>
                    </td>

                    <td>{{ $instance->order->purchase_order_name }}</td>
                    <td>{{ $instance->order->purchase_order_date->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->order->manufacturer->name }}</td>
                    <td>{{ $instance->payer->name }}</td>
                    <td>{{ $instance->currency->name }}</td>
                    <td>{{ $instance->total_price }}</td>
                    <td>{{ $instance->terms }} %</td>
                    <td>{{ $instance->payment_due }}</td>
                    <td>{{ $instance->prepayment_amount }}</td>
                    <td>{{ $instance->sent_for_payment_date?->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->amount_paid }}</td>
                    <td>{{ $instance->payment_date?->isoformat('DD MMM Y') }}</td>
                    <td>{{ $instance->payment_difference }}</td>
                    <td>{{ $instance->status }}</td>
                    <td>{{ $instance->group_name }}</td>
                </tr>
            @endforeach
        </tbody> {{-- Body end --}}
    </table>
</div>
