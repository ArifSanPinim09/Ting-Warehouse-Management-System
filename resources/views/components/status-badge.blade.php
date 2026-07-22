@props(['status' => '', 'size' => 'sm'])

@php
    $config = match($status) {
        // Sprint 1: 13 status sesuai dokumen client
        'OPEN' => ['label' => 'Open', 'class' => 'ds-badge-success'],
        'LAST_CLAIM' => ['label' => 'Last Claim', 'class' => 'ds-badge-warning'],
        'CLOSED' => ['label' => 'Closed', 'class' => 'ds-badge-neutral'],
        'REQUEST_TO_SEND' => ['label' => 'Request to Send', 'class' => 'ds-badge-warning'],
        'SEND_TO_CARGO' => ['label' => 'Send to Cargo', 'class' => 'ds-badge-info'],
        'ARRIVED_AT_CARGO' => ['label' => 'Arrived at Cargo', 'class' => 'ds-badge-info'],
        'WAITING_FOR_DEPARTURE' => ['label' => 'Waiting for Departure', 'class' => 'ds-badge-warning'],
        'DEPARTURE' => ['label' => 'Departure', 'class' => 'ds-badge-info'],
        'ARRIVED_INA' => ['label' => 'Arrived INA', 'class' => 'ds-badge-warning'],
        'REDLINE' => ['label' => 'Redline', 'class' => 'ds-badge-danger'],
        'STEVEDORING' => ['label' => 'Stevedoring', 'class' => 'ds-badge-info'],
        'CHECKED_BY_WH' => ['label' => 'Checked by WH', 'class' => 'ds-badge-info'],
        'INVOICE' => ['label' => 'Invoice', 'class' => 'ds-badge-info'],
        'DONE' => ['label' => 'Done', 'class' => 'ds-badge-success'],
        'REQUEST_TO_CLOSE' => ['label' => 'Request to Close', 'class' => 'ds-badge-warning'],
        // Invoice payment statuses
        'waiting_payment' => ['label' => 'Menunggu Pembayaran', 'class' => 'ds-badge-warning'],
        'waiting_verification' => ['label' => 'Menunggu Verifikasi', 'class' => 'ds-badge-info'],
        'verified' => ['label' => 'Terverifikasi', 'class' => 'ds-badge-success'],
        // Checkout statuses
        'request' => ['label' => 'Menunggu Proses', 'class' => 'ds-badge-warning'],
        'on_process' => ['label' => 'Sedang Diproses', 'class' => 'ds-badge-info'],
        'sent' => ['label' => 'Terkirim', 'class' => 'ds-badge-success'],
        'open' => ['label' => 'Terbuka', 'class' => 'ds-badge-warning'],
        // Complain statuses
        'in_review' => ['label' => 'Sedang Ditinjau', 'class' => 'ds-badge-info'],
        'processing' => ['label' => 'Sedang Diproses', 'class' => 'ds-badge-info'],
        'resolved' => ['label' => 'Selesai', 'class' => 'ds-badge-success'],
        // User statuses
        'pending' => ['label' => 'Menunggu Aktivasi', 'class' => 'ds-badge-warning'],
        'active' => ['label' => 'Aktif', 'class' => 'ds-badge-success'],
        'inactive' => ['label' => 'Nonaktif', 'class' => 'ds-badge-danger'],
        // Legacy aliases (backward compatibility)
        'SENT_TO_CARGO' => ['label' => 'Send to Cargo', 'class' => 'ds-badge-info'],
        'OTW_INA' => ['label' => 'Arrived INA', 'class' => 'ds-badge-warning'],
        'UP_INVOICE' => ['label' => 'Invoice', 'class' => 'ds-badge-info'],
        'LAST_SETOR' => ['label' => 'Last Claim', 'class' => 'ds-badge-warning'],
        default => ['label' => $status, 'class' => 'ds-badge-neutral'],
    };
@endphp

<span {{ $attributes->merge(['class' => $config['class'] . ($size === 'lg' ? ' text-body px-3 py-1' : '')]) }}>
    {{ $config['label'] }}
</span>
