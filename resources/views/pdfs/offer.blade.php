<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Oferta #{{ $offer->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #64748b; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border-bottom: 1px solid #e2e8f0; padding: 8px; text-align: left; }
        th { background: #f1f5f9; }
        .text-right { text-align: right; }
        .total-row td { font-weight: bold; border-top: 2px solid #1e293b; }
        .section { margin-top: 24px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; background: #dbeafe; color: #1e3a8a; font-size: 11px; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>CCTV Security</h1>
            <p class="muted">Sisteme de supraveghere video<br>contact@cctv-security.test &middot; 0700 000 000</p>
        </div>
        <div style="text-align: right;">
            <h1>Oferta #{{ $offer->id }}</h1>
            <p class="muted">Data: {{ $offer->created_at->format('d.m.Y') }}</p>
            @if ($offer->valid_until)
                <p class="muted">Valabila pana la: {{ $offer->valid_until->format('d.m.Y') }}</p>
            @endif
            <span class="badge">{{ strtoupper($offer->status) }}</span>
        </div>
    </div>

    <div class="section">
        <strong>Client:</strong> {{ $offer->client->name }}
        @if ($offer->client->company_name) ({{ $offer->client->company_name }}) @endif<br>
        @if ($offer->client->phone) Telefon: {{ $offer->client->phone }}<br> @endif
        @if ($offer->client->email) Email: {{ $offer->client->email }}<br> @endif
        @if ($offer->client->address) Adresa: {{ $offer->client->address }}, {{ $offer->client->city }} @endif
    </div>

    <div class="section">
        <strong>{{ $offer->title }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descriere</th>
                <th class="text-right">Cantitate</th>
                <th class="text-right">Pret unitar</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offer->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }} lei</td>
                    <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 2) }} lei</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-right">Total</td>
                <td class="text-right">{{ number_format($offer->total_amount, 2) }} lei</td>
            </tr>
        </tbody>
    </table>

    @if ($offer->notes)
        <div class="section">
            <strong>Note:</strong>
            <p>{{ $offer->notes }}</p>
        </div>
    @endif

    <div class="section muted">
        Oferta generata automat prin platforma CCTV Security. Preturile sunt exprimate in lei si nu includ eventuale
        costuri suplimentare de instalare in conditii speciale (inaltime, distanta extinsa de cablare).
    </div>
</body>
</html>
