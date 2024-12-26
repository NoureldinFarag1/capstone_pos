<table>
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Quantity</th>
            <th>Selling Price</th>
            <th>Discount Type</th>
            <th>Discount Value</th>
            <th>Price After Sale</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->name }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->selling_price }}</td>
                <td>{{ $item->discount_type }}</td>
                <td>{{ $item->discount_value}}</td>
                <td>{{ $item->priceAfterSale() }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
