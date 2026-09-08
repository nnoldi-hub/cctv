<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Raport instalare #{{ $installation->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #64748b; }
        .section { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { padding: 4px 0; }
        .checklist-item { padding: 4px 0; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; background: #dbeafe; color: #1e3a8a; font-size: 11px; }
    </style>
</head>
<body>
    <h1>Raport tehnic - {{ $installation->type === 'interventie' ? 'Interventie' : 'Instalare' }} #{{ $installation->id }}</h1>
    <p class="muted">Generat la {{ now()->format('d.m.Y H:i') }}</p>

    <div class="section">
        <table>
            <tr>
                <td style="width: 30%;"><strong>Client:</strong></td>
                <td>{{ $installation->client->name }}</td>
            </tr>
            <tr>
                <td><strong>Adresa:</strong></td>
                <td>{{ $installation->address ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Tehnician:</strong></td>
                <td>{{ $installation->technician->name ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Data programata:</strong></td>
                <td>{{ $installation->scheduled_at?->format('d.m.Y H:i') ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td><span class="badge">{{ strtoupper($installation->status) }}</span></td>
            </tr>
        </table>
    </div>

    @if (!empty($installation->checklist))
        <div class="section">
            <strong>Checklist instalare</strong>
            @foreach ($installation->checklist as $item)
                <div class="checklist-item">
                    [{{ $item['done'] ? 'X' : ' ' }}] {{ $item['label'] }}
                </div>
            @endforeach
        </div>
    @endif

    @if ($installation->notes)
        <div class="section">
            <strong>Note tehnice</strong>
            <p>{{ $installation->notes }}</p>
        </div>
    @endif

    @if ($installation->labor_hours || $installation->materials)
        <div class="section">
            <strong>Executie</strong>
            <table>
                <tr><td style="width: 30%;"><strong>Ore lucrate:</strong></td><td>{{ $installation->labor_hours ?? '-' }}</td></tr>
                <tr><td><strong>Materiale:</strong></td><td>{{ !empty($installation->materials) ? implode(', ', $installation->materials) : '-' }}</td></tr>
            </table>
        </div>
    @endif

    @if ($installation->customer_name || $installation->customer_notes)
        <div class="section">
            <strong>Confirmare client</strong>
            <p>Nume: {{ $installation->customer_name ?? '-' }}</p>
            <p>{{ $installation->customer_notes }}</p>
        </div>
    @endif

    <div class="section muted">
        Raport generat automat prin platforma CCTV Security.
    </div>
</body>
</html>
