<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: sans-serif; font-size: 11px; color: #1a1a1a; padding: 15mm 10mm; }
  .header { text-align: center; border-bottom: 3px solid #1a1a1a; padding-bottom: 8px; margin-bottom: 15px; }
  .header h1 { font-size: 18px; }
  .header p { font-size: 10px; color: #555; margin-top: 2px; }
  .box-info { display: flex; gap: 30px; margin-bottom: 15px; font-size: 11px; }
  .box-info b { color: #000; }
  table { width: 100%; border-collapse: collapse; }
  th { background: #f5f5f5; padding: 6px 4px; text-align: left; font-size: 9px; text-transform: uppercase; border: 1px solid #ddd; }
  td { padding: 5px 4px; border: 1px solid #ddd; font-size: 10px; }
  .photo { width: 40px; height: 40px; object-fit: cover; border-radius: 2px; }
  .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #999; }
</style>
</head>
<body>
  <div class="header">
    <h1>IMPORT PER BOX — {{ $box->box_code }}</h1>
    <p>Ting Warehouse · {{ now()->format('d M Y') }}</p>
  </div>

  <div class="box-info">
    <div><b>Box Code:</b> {{ $box->box_code }}</div>
    <div><b>Method:</b> {{ strtoupper($box->method) }}</div>
    <div><b>Type:</b> {{ ucfirst($box->type) }}</div>
    <div><b>Status:</b> {{ $box->getStatusLabelAttribute() }}</div>
    @if($box->matched_batch)
    <div><b>Matched Batch:</b> {{ $box->matched_batch }}</div>
    @endif
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:4%">No</th>
        <th style="width:18%">Tracking Number</th>
        <th style="width:15%">Nama Barang</th>
        <th style="width:10%">Customer</th>
        <th style="width:6%">Qty</th>
        <th style="width:10%">Berat (kg)</th>
        <th style="width:12%">Dimensi (P×L×T)</th>
        <th style="width:8%">Photo</th>
        <th style="width:7%">Tags</th>
      </tr>
    </thead>
    <tbody>
      @foreach($box->items as $i => $item)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td style="font-family:monospace">{{ $item->resi_number }}</td>
        <td>{{ $item->name }}</td>
        <td>{{ $item->customer->name ?? '-' }}</td>
        <td>{{ $item->quantity }}</td>
        <td>{{ $item->whChinaData?->berat ?? '-' }}</td>
        <td>{{ $item->whChinaData?->ukuran_box ?? '-' }}</td>
        <td>
          @if($item->whChinaData?->foto_barang)
          <img src="{{ asset('storage/' . $item->whChinaData->foto_barang) }}" class="photo">
          @else
          -
          @endif
        </td>
        <td>
          @if($item->is_sensitive) S @endif
          @if($item->is_garment) G @endif
          @if(!$item->is_sensitive && !$item->is_garment) - @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="footer">
    Dokumen ini untuk pihak Becuk · {{ $box->items->count() }} items · Ting Warehouse
  </div>
</body>
</html>
