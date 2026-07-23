<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: sans-serif; font-size: 12px; color: #1a1a1a; padding: 20mm 15mm; }
  .header { text-align: center; border-bottom: 3px solid #1a1a1a; padding-bottom: 10px; margin-bottom: 20px; }
  .header h1 { font-size: 22px; font-weight: bold; }
  .header p { font-size: 11px; color: #555; margin-top: 3px; }
  .info-grid { display: flex; justify-content: space-between; margin-bottom: 20px; }
  .info-block { font-size: 11px; line-height: 1.6; }
  .info-block .label { color: #888; font-size: 9px; text-transform: uppercase; }
  .info-block .value { font-weight: 600; }
  table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
  th { background: #f5f5f5; padding: 8px 6px; text-align: left; font-size: 10px; text-transform: uppercase; border: 1px solid #ddd; }
  td { padding: 6px; border: 1px solid #ddd; font-size: 11px; }
  .total-section { margin-left: auto; width: 50%; }
  .total-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 11px; }
  .total-row.grand { border-top: 2px solid #1a1a1a; padding-top: 8px; margin-top: 8px; font-size: 14px; font-weight: bold; }
  .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #999; }
  .badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 9px; font-weight: 600; }
  .badge-sensitive { background: #fee2e2; color: #991b1b; }
  .badge-garment { background: #dbeafe; color: #1e40af; }
</style>
</head>
<body>
  <div class="header">
    <h1>TING WAREHOUSE</h1>
    <p>Freight Forwarding China → Indonesia · Faktur Pengiriman</p>
  </div>

  <div class="info-grid">
    <div class="info-block">
      <div class="label">Nomor Invoice</div>
      <div class="value">{{ $checkout->invoice->invoice_number }}</div>
      <div class="label" style="margin-top:8px">Tanggal</div>
      <div>{{ $checkout->invoice->created_at->format('d M Y') }}</div>
    </div>
    <div class="info-block">
      <div class="label">Customer</div>
      <div class="value">{{ $checkout->customer->name }}</div>
      <div class="label" style="margin-top:8px">Box</div>
      <div>{{ $checkout->invoice->box->box_code ?? '-' }}</div>
    </div>
    <div class="info-block">
      <div class="label">Penerima</div>
      <div class="value">{{ $checkout->recipient_name }}</div>
      <div>{{ $checkout->recipient_phone }}</div>
      <div style="font-size:10px; max-width:200px">{{ $checkout->address }}</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th style="width:5%">No</th>
        <th style="width:30%">Nama Barang</th>
        <th style="width:15%">No. Resi</th>
        <th style="width:8%">Qty</th>
        <th style="width:12%">Tags</th>
      </tr>
    </thead>
    <tbody>
      @foreach($checkout->invoice->box->items as $i => $item)
      <tr>
        <td>{{ $i + 1 }}</td>
        <td>{{ $item->name }}</td>
        <td style="font-family:monospace; font-size:10px">{{ $item->resi_number }}</td>
        <td>{{ $item->quantity }}</td>
        <td>
          @if($item->is_sensitive) <span class="badge badge-sensitive">S</span> @endif
          @if($item->is_garment) <span class="badge badge-garment">G</span> @endif
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="total-section">
    <div class="total-row"><span>Fee TAX</span><span>Rp {{ number_format($checkout->invoice->fee_tax, 0, ',', '.') }}</span></div>
    <div class="total-row"><span>Fee WH</span><span>Rp {{ number_format($checkout->invoice->fee_wh, 0, ',', '.') }}</span></div>
    <div class="total-row"><span>Fee Packing</span><span>Rp {{ number_format($checkout->fee_packing ?? $checkout->invoice->fee_packing, 0, ',', '.') }}</span></div>
    @if($checkout->invoice->add_on > 0)
    <div class="total-row"><span>Add On</span><span>Rp {{ number_format($checkout->invoice->add_on, 0, ',', '.') }}</span></div>
    @endif
    <div class="total-row"><span>Ongkir Ekspedisi</span><span>Rp {{ number_format($checkout->ongkir, 0, ',', '.') }}</span></div>
    <div class="total-row grand"><span>Grand Total</span><span>Rp {{ number_format($checkout->invoice->grand_total + ($checkout->fee_packing ?? 0) + $checkout->ongkir, 0, ',', '.') }}</span></div>
  </div>

  <div class="footer">
    Invoice ini sah tanpa tanda tangan basah · Ting Warehouse · {{ now()->format('d/m/Y') }}
  </div>
</body>
</html>
