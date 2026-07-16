@extends('layouts.app')

@section('title', 'Gap Analysis ASN')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }

    .gap-container {
        padding: 20px;
        background: #fff;
    }

    .page-title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #1f2937;
    }

    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        gap: 12px;
    }

    .controls-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .show-label {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    .show-select {
        padding: 6px 10px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 12px;
        cursor: pointer;
        background: white;
        color: #1f2937;
    }

    .show-select:focus {
        outline: none;
        border-color: #2563eb;
    }

    .export-btn {
        background: #2692b3;
        color: white;
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background 0.2s;
    }

    .export-btn:hover {
        background: #146a84;
        color: white;
    }

    .table-wrapper {
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .gap-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .gap-table thead {
        background: #f3f4f6;
        border-bottom: 1px solid #e5e7eb;
    }

    .gap-table thead th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #1f2937;
        font-size: 12px;
        border-right: 1px solid #e5e7eb;
    }

    .gap-table thead th:last-child {
        border-right: none;
    }

    .gap-table tbody td {
        padding: 12px;
        border-bottom: 1px solid #e5e7eb;
        border-right: 1px solid #e5e7eb;
        color: #1f2937;
    }

    .gap-table tbody td:last-child {
        border-right: none;
    }

    .gap-table tbody tr:last-child td {
        border-bottom: none;
    }

    .gap-table tbody tr:hover {
        background: #f9fafb;
    }

    .no-cell {
        text-align: center;
        width: 5%;
    }

    .kode-cell {
        text-align: center;
        width: 10%;
    }

    .kode-badge {
        background: #8daff9;
        color: white;
        padding: 4px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
        min-width: 45px;
    }

    .opd-cell {
        font-weight: 500;
        color: #1f2937;
        width: 14%;
    }

    .unor-cell {
        color: #6b7280;
        width: 14%;
    }

    .nama-cell {
        font-weight: 500;
        color: #1f2937;
        width: 35%;
    }

    .value-cell {
        text-align: center;
        width: 4%;
    }

    .value-badge {
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 500;
        font-size: 12px;
        display: inline-block;
        min-width: 35px;
    }

    .bezetting-badge {
        background: #cffafe;
        color: #0891b2;
    }

    .kebutuhan-badge {
        background: #f8f098;
        color: #b45309;
    }

    .gap-positive {
        background: #dcfce7;
        color: #166534;
    }

    .gap-negative {
        background: #fee2e2;
        color: #991b1b;
    }

    .gap-zero {
        background: #e9ecf2;
        color: #4b5563;
    }

    .status-cell {
        text-align: center;
        width: 12%;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 3px;
        font-weight: 500;
        font-size: 11px;
        text-transform: uppercase;
        display: inline-block;
    }

    .status-kekurangan {
        background: #f8ead4;
        color: #a97601;
    }

    .status-terpenuhi {
        background: #dcfce7;
        color: #166534;
    }

    .status-kelebihan {
        background: #fef3c7;
        color: #b45309;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .pagination-btn {
        padding: 6px 10px;
        border: 1px solid #d1d5db;
        background: white;
        color: #2563eb;
        text-decoration: none;
        border-radius: 3px;
        font-weight: 500;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .pagination-btn:hover {
        background: #f3f4f6;
        border-color: #2563eb;
    }

    .pagination-btn.active {
        background: #2563eb;
        color: white;
        border-color: #2563eb;
    }

    .pagination-btn.disabled {
        opacity: 0.4;
        cursor: not-allowed;
        background: #f9fafb;
        color: #9ca3af;
    }

    .pagination-dots {
        padding: 6px 4px;
        color: #9ca3af;
        font-size: 12px;
    }

    .summary-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .summary-card {
        background: white;
        padding: 16px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        text-align: center;
    }

    .summary-label {
        font-size: 11px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        margin-bottom: 8px;
        letter-spacing: 0.3px;
    }

    .summary-value {
        font-size: 28px;
        font-weight: 700;
        color: #1f2937;
    }

    .summary-card.danger .summary-value {
        color: #dc2626;
    }

    .summary-card.success .summary-value {
        color: #16a34a;
    }

    .summary-card.warning .summary-value {
        color: #d97706;
    }

    .summary-card.info .summary-value {
        color: #2563eb;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #9ca3af;
    }

    .empty-state i {
        font-size: 32px;
        margin-bottom: 12px;
        opacity: 0.4;
    }

    .empty-state p {
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .table-controls {
            flex-direction: column;
            align-items: flex-start;
        }

        .controls-left {
            width: 100%;
        }

        .gap-table {
            font-size: 12px;
        }

        .gap-table thead th,
        .gap-table tbody td {
            padding: 10px 8px;
            font-size: 11px;
        }

        .summary-container {
            grid-template-columns: repeat(2, 1fr);
        }

        .summary-value {
            font-size: 22px;
        }
    }
</style>

<div class="gap-container">

    <!-- Table Controls -->
    <div class="table-controls">
        <div class="controls-left">
            <span class="show-label">Show</span>
            <select class="show-select" id="perPageSelect" onchange="changePerPage(this.value)">
                <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
            <span class="show-label">entries</span>
        </div>
        <a href="{{ route('laporan.export-gap-excel') }}" class="export-btn">
            <i class="fas fa-download"></i> Export
        </a>
    </div>

    <!-- Table -->
    <div class="table-wrapper">
        <table class="gap-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Perangkat Daerah</th>
                    <th>Unit Organisasi</th>
                    <th>Nama Jabatan</th>
                    <th>B</th>
                    <th>K</th>
                    <th>+/-</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @php 
                    $startNo = (($page - 1) * $perPage) + 1;
                    $no = $startNo;
                @endphp
                @forelse($jabatan as $item)
                    <tr>
                        <td class="no-cell">{{ $no++ }}</td>
                        <td class="kode-cell">
                            <span class="kode-badge">{{ $item->kode ?? '-' }}</span>
                        </td>
                        <td class="opd-cell">{{ $item->unitOrganisasi->perangkatDaerah->nama ?? '-' }}</td>
                        <td class="unor-cell">{{ $item->unitOrganisasi->nama ?? '-' }}</td>
                        <td class="nama-cell">{{ $item->nama }}</td>
                        <td class="value-cell">
                            <span class="value-badge bezetting-badge">{{ (int)$item->b }}</span>
                        </td>
                        <td class="value-cell">
                            <span class="value-badge kebutuhan-badge">{{ (int)$item->k }}</span>
                        </td>
                        <td class="value-cell">
                            @if($item->gap > 0)
                                <span class="value-badge gap-positive">+{{ $item->gap }}</span>
                            @elseif($item->gap < 0)
                                <span class="value-badge gap-negative">{{ $item->gap }}</span>
                            @else
                                <span class="value-badge gap-zero">0</span>
                            @endif
                        </td>
                        <td class="status-cell">
                            @if($item->gap < 0)
                                <span class="status-badge status-kekurangan">Kekurangan</span>
                            @elseif($item->gap > 0)
                                <span class="status-badge status-kelebihan">Kelebihan</span>
                            @else
                                <span class="status-badge status-terpenuhi">Terpenuhi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Tidak ada data untuk ditampilkan</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
        <div class="pagination-wrapper">
            @if($page > 1)
                <a href="{{ route('laporan.gap-analysis', ['page' => 1, 'per_page' => $perPage]) }}" class="pagination-btn">«</a>
                <a href="{{ route('laporan.gap-analysis', ['page' => $page - 1, 'per_page' => $perPage]) }}" class="pagination-btn">‹</a>
            @else
                <button class="pagination-btn disabled">«</button>
                <button class="pagination-btn disabled">‹</button>
            @endif

            @php
                $startPage = max(1, $page - 1);
                $endPage = min($totalPages, $page + 1);
            @endphp

            @for($i = $startPage; $i <= $endPage; $i++)
                @if($i == $page)
                    <button class="pagination-btn active">{{ $i }}</button>
                @else
                    <a href="{{ route('laporan.gap-analysis', ['page' => $i, 'per_page' => $perPage]) }}" class="pagination-btn">{{ $i }}</a>
                @endif
            @endfor

            @if($page < $totalPages)
                <a href="{{ route('laporan.gap-analysis', ['page' => $page + 1, 'per_page' => $perPage]) }}" class="pagination-btn">›</a>
                <a href="{{ route('laporan.gap-analysis', ['page' => $totalPages, 'per_page' => $perPage]) }}" class="pagination-btn">»</a>
            @else
                <button class="pagination-btn disabled">›</button>
                <button class="pagination-btn disabled">»</button>
            @endif
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="summary-container" style="margin-top: 24px;">
        <div class="summary-card danger">
            <div class="summary-label">Kekurangan</div>
            <div class="summary-value">{{ $jabatanAll->where('gap', '<', 0)->count() }}</div>
        </div>
        <div class="summary-card success">
            <div class="summary-label">Terpenuhi</div>
            <div class="summary-value">{{ $jabatanAll->where('gap', 0)->count() }}</div>
        </div>
        <div class="summary-card warning">
            <div class="summary-label">Kelebihan</div>
            <div class="summary-value">{{ $jabatanAll->where('gap', '>', 0)->count() }}</div>
        </div>
        <div class="summary-card info">
            <div class="summary-label">Total Gap</div>
            <div class="summary-value">{{ $jabatanAll->sum('gap') }}</div>
        </div>
    </div>
</div>

<script>
    function changePerPage(value) {
        window.location.href = "{{ route('laporan.gap-analysis') }}?page=1&per_page=" + value;
    }
</script>
@endsection
