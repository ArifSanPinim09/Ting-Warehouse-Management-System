@props(['status' => '', 'size' => 'sm'])

@php
    $config = match($status) {
        'OPEN' => ['label' => 'Terbuka', 'class' => 'ds-badge-success'],
        'SENT_TO_CARGO' => ['label' => 'Dikirim ke Cargo', 'class' => 'ds-badge-info'],
        'OTW_INA' => ['label' => 'Dalam Perjalanan', 'class' => 'ds-badge-warning'],
        'UP_INVOICE' => ['label' => 'Invoice Dibuat', 'class' => 'ds-badge-info'],
        'DONE' => ['label' => 'Selesai', 'class' => 'ds-badge-success'],
        'waiting_payment' => ['label' => 'Menunggu Pembayaran', 'class' => 'ds-badge-warning'],
        'waiting_verification' => ['label' => 'Menunggu Verifikasi', 'class' => 'ds-badge-info'],
        'verified' => ['label' => 'Terverifikasi', 'class' => 'ds-badge-success'],
        'request' => ['label' => 'Menunggu Proses', 'class' => 'ds-badge-warning'],
        'on_process' => ['label' => 'Sedang Diproses', 'class' => 'ds-badge-info'],
        'sent' => ['label' => 'Terkirim', 'class' => 'ds-badge-success'],
        'open' => ['label' => 'Terbuka', 'class' => 'ds-badge-warning'],
        'in_review' => ['label' => 'Sedang Ditinjau', 'class' => 'ds-badge-info'],
        'processing' => ['label' => 'Sedang Diproses', 'class' => 'ds-badge-info'],
        'resolved' => ['label' => 'Selesai', 'class' => 'ds-badge-success'],
        'pending' => ['label' => 'Menunggu Aktivasi', 'class' => 'ds-badge-warning'],
        'active' => ['label' => 'Aktif', 'class' => 'ds-badge-success'],
        'inactive' => ['label' => 'Nonaktif', 'class' => 'ds-badge-danger'],
        default => ['label' => $status, 'class' => 'ds-badge-neutral'],
    };
@endphp

<span {{ $attributes->merge(['class' => $config['class'] . ($size === 'lg' ? ' text-sm px-3 py-1' : '')]) }}>
    {{ $config['label'] }}
</span>
