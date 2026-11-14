<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Barcode — Alysa Glasses</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f5; padding: 24px; color: #1e293b; }
        .toolbar { max-width: 900px; margin: 0 auto 20px; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .toolbar h1 { font-size: 18px; }
        .toolbar .sub { font-size: 12px; color: #64748b; }
        .btn { padding: 9px 16px; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-print { background: #0f172a; color: #fff; }
        .btn-back { background: #e2e8f0; color: #334155; }
        .grid { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); gap: 14px; }
        .label { background: #fff; border: 1px dashed #94a3b8; border-radius: 8px; padding: 12px 10px; text-align: center; page-break-inside: avoid; }
        .label .name { font-size: 11px; font-weight: 700; color: #0f172a; line-height: 1.3; min-height: 28px; }
        .label .price { font-size: 13px; font-weight: 800; color: #be123c; margin: 4px 0; }
        .label svg { max-width: 100%; height: auto; }
        .label .code { font-size: 12px; font-weight: 700; letter-spacing: 1px; color: #334155; margin-top: 2px; font-family: 'Courier New', monospace; }
        .empty { grid-column: 1/-1; text-align: center; color: #64748b; padding: 40px; font-size: 14px; }
        @media print {
            body { background: #fff; padding: 0; }
            .toolbar { display: none; }
            .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 6mm; }
            .label { border: 1px solid #ddd; border-radius: 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <div>
            <h1>🏷️ Label Barcode Frame ({{ count($products) }})</h1>
            <div class="sub">Gunakan tombol Cetak di bawah, lalu tempel pada tag / kemasan tiap frame.</div>
        </div>
        <div style="display:flex; gap:8px;">
            <a class="btn btn-back" href="{{ route('admin.katalog.index') }}">← Katalog</a>
            <button class="btn btn-print" onclick="window.print()">🖨️ Cetak Label</button>
        </div>
    </div>

    <div class="grid">
        @forelse($products as $p)
            <div class="label">
                <div class="name">{{ $p->name }}</div>
                <div class="price">Rp {{ number_format($p->effective_price, 0, ',', '.') }}</div>
                {!! $p->barcode_svg !!}
                <div class="code">{{ $p->barcode }}</div>
            </div>
        @empty
            <div class="empty">Belum ada produk dengan barcode. Klik tombol <strong>Generate Barcode</strong> di halaman katalog terlebih dahulu.</div>
        @endforelse
    </div>
</body>
</html>
