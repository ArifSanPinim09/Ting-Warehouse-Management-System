<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 0; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: sans-serif; width: 100mm; height: 150mm; padding: 5mm; }
  .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 3mm; margin-bottom: 3mm; }
  .header h1 { font-size: 14px; font-weight: bold; }
  .header p { font-size: 9px; color: #555; }
  .label-row { display: flex; justify-content: space-between; margin-bottom: 2mm; font-size: 10px; }
  .label-row .key { font-weight: bold; color: #333; }
  .label-row .val { color: #000; }
  .barcode { text-align: center; margin: 3mm 0; }
  .barcode .number { font-family: monospace; font-size: 16px; font-weight: bold; letter-spacing: 2px; }
  .address { border: 1px solid #ccc; padding: 3mm; border-radius: 2mm; margin-top: 2mm; }
  .address .title { font-size: 9px; color: #666; text-transform: uppercase; margin-bottom: 1mm; }
  .address .name { font-size: 12px; font-weight: bold; }
  .address .phone { font-size: 10px; color: #444; }
  .address .addr { font-size: 10px; color: #333; margin-top: 1mm; }
  .footer { position: absolute; bottom: 5mm; left: 5mm; right: 5mm; text-align: center; font-size: 8px; color: #999; }
</style>
</head>
<body>
  <div class="header">
    <h1>TING WAREHOUSE</h1>
    <p>Freight Forwarding China → Indonesia</p>
  </div>

  <div class="label-row">
    <span class="key">No. Invoice:</span>
    <span class="val">{{ $checkout->invoice->invoice_number ?? '-' }}</span>
  </div>
  <div class="label-row">
    <span class="key">Tracking:</span>
    <span class="val">{{ $checkout->tracking_number ?? '-' }}</span>
  </div>
  <div class="label-row">
    <span class="key">Box:</span>
    <span class="val">{{ $checkout->invoice->box->box_code ?? '-' }}</span>
  </div>

  <div class="barcode">
    <div class="number">{{ $checkout->tracking_number ?? $checkout->invoice->invoice_number }}</div>
    <div style="font-size: 8px; color: #999;">|||||||||||||||||||||||||||||||||||</div>
  </div>

  <div class="address">
    <div class="title">Penerima</div>
    <div class="name">{{ $checkout->recipient_name }}</div>
    <div class="phone">{{ $checkout->recipient_phone }}</div>
    <div class="addr">{{ $checkout->address }}</div>
  </div>

  @if($checkout->address_type === 'dropship')
  <div class="address">
    <div class="title">Pengirim</div>
    <div class="name">{{ $checkout->sender_name }}</div>
    <div class="phone">{{ $checkout->sender_phone }}</div>
  </div>
  @endif

  <div class="footer">
    Dicetak: {{ now()->format('d/m/Y H:i') }} · {{ $checkout->customer->name ?? '' }}
  </div>
</body>
</html>
