@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('css')
<style>
    * {
        margin: 0;
        padding: 0;
    }

    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        border-radius: 6px;
        overflow: hidden;
    }

    .card-header {
        background-color: #1a3a52;
        color: white;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background-color: #3b82f6;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.2s ease;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        color: white;
        text-decoration: none;
    }

    .control-bar {
        padding: 12px 16px;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .control-info {
        font-size: 0.85rem;
        color: #6b7280;
        font-weight: 500;
    }

    .per-page-control {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .per-page-control label {
        font-size: 0.85rem;
        color: #6b7280;
        margin: 0;
    }

    .per-page-control select {
        padding: 4px 8px;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        font-size: 0.85rem;
        cursor: pointer;
    }

    .hierarchy-container {
        padding: 12px 16px;
        background-color: #ffffff;
    }

    .hierarchy-level-1 {
        margin-bottom: 12px;
    }

    /* Level 1: Perangkat Daerah */
    .perangkat-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px;
        background-color: #f3f4f6;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        user-select: none;
        font-weight: 600;
    }

    .perangkat-header:hover {
        background-color: #e5e7eb;
    }

    .toggle-btn-1 {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        cursor: pointer;
        transition: transform 0.2s ease;
        color: #4b5563;
        font-size: 10px;
        font-weight: bold;
        flex-shrink: 0;
    }

    .toggle-btn-1.collapsed {
        transform: rotate(-90deg);
    }

    .perangkat-name {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .perangkat-icon {
        font-size: 1.1rem;
        color: #4b5563;
    }

    .perangkat-label {
        font-weight: 600;
        color: #1f2937;
    }

    .units-container {
        display: none;
        margin-left: 20px;
        margin-top: 8px;
        border-left: 3px solid #e5e7eb;
        padding-left: 12px;
    }

    .units-container.show {
        display: block;
    }

    /* Level 2: Unit Organisasi */
    .unit-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background-color: #f9fafb;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.2s ease;
        user-select: none;
        font-weight: 600;
        margin-bottom: 8px;
        border: 1px solid #e5e7eb;
    }

    .unit-header:hover {
        background-color: #f3f4f6;
    }

    .toggle-btn-2 {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        cursor: pointer;
        transition: transform 0.2s ease;
        color: #4b5563;
        font-size: 9px;
        font-weight: bold;
        flex-shrink: 0;
    }

    .toggle-btn-2.collapsed {
        transform: rotate(-90deg);
    }

    .unit-icon {
        font-size: 1rem;
        color: #4b5563;
    }

    .unit-name {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .unit-label {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
    }

    .unit-count {
        background-color: #93c5fd;
        color: #1e40af;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
    }

    .jabatan-container {
        display: none;
        margin-left: 20px;
        margin-top: 8px;
        border-left: 2px solid #e5e7eb;
        padding-left: 12px;
    }

    .jabatan-container.show {
        display: block;
    }

    /* Level 3: Jabatan */
    .jabatan-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        background-color: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        margin-bottom: 6px;
        transition: background-color 0.2s ease;
    }

    .jabatan-item:hover {
        background-color: #f9fafb;
    }

    .jabatan-icon {
        font-size: 0.9rem;
        color: #9ca3af;
        flex-shrink: 0;
    }

    .jabatan-content {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.9rem;
        flex-wrap: wrap;
    }

    .jabatan-kode {
        padding: 2px 6px;
        background-color: #e0e7ff;
        color: #312e81;
        border-radius: 3px;
        font-weight: 600;
        font-size: 0.8rem;
        min-width: 50px;
        text-align: center;
        flex-shrink: 0;
    }

    .jabatan-nama {
        flex: 1;
        color: #374151;
        min-width: 250px;
        word-wrap: break-word;
    }

    .jabatan-stats {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .stat-badge {
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
        min-width: 35px;
        text-align: center;
    }

    .badge-b {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-k {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-gap {
        min-width: 40px;
    }

    .badge-gap-positive {
        background-color: #fecaca;
        color: #7f1d1d;
    }

    .badge-gap-negative {
        background-color: #bbf7d0;
        color: #065f46;
    }

    .badge-gap-zero {
        background-color: #d1d5db;
        color: #374151;
    }

    .jabatan-actions {
        display: flex;
        gap: 4px;
        flex-shrink: 0;
    }

    .btn-action {
        padding: 4px 8px;
        border: none;
        border-radius: 3px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
    }

    .btn-edit {
        background-color: #fbbf24;
        color: white;
    }

    .btn-edit:hover {
        background-color: #f59e0b;
        color: white;
    }

    .btn-delete {
        background-color: #ef4444;
        color: white;
    }

    .btn-delete:hover {
        background-color: #dc2626;
        color: white;
    }

    .empty-state {
        padding: 40px 16px;
        text-align: center;
        color: #9ca3af;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 16px;
    }

    .pagination-footer {
        padding: 12px 16px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: center;
        gap: 4px;
        flex-wrap: wrap;
    }

    .pagination-footer a,
    .pagination-footer span {
        padding: 4px 8px;
        border: 1px solid #d1d5db;
        border-radius: 3px;
        font-size: 0.8rem;
        color: #3b82f6;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .pagination-footer a:hover {
        background-color: #eff6ff;
    }

    .pagination-footer .active span {
        background-color: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    @media (max-width: 1200px) {
        .jabatan-content {
            flex-wrap: wrap;
        }

        .jabatan-nama {
            min-width: 200px;
        }

        .card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .control-bar {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-briefcase"></i> Master Jabatan
                </div>
                <a href="{{ route('jabatan.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i> Tambah Jabatan
                </a>
            </div>

            <!-- Control Bar -->
            <div class="control-bar">
                <div class="control-info">
                    Menampilkan {{ $perangkatDaerahs->firstItem() ?? 0 }} - {{ $perangkatDaerahs->lastItem() ?? 0 }} dari {{ $perangkatDaerahs->total() }} Perangkat Daerah
                </div>
                <div class="per-page-control">
                    <label for="perPageSelect">Per Halaman:</label>
                    <select id="perPageSelect" onchange="changePerPage(this.value)">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ $perPage == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    </select>
                </div>
            </div>

            <!-- Hierarchy Container -->
            <div class="hierarchy-container">
                @if(count($groupedByPerangkat) > 0)
                    @foreach($groupedByPerangkat as $perangkatGroup)
                        <div class="hierarchy-level-1">
                            <!-- Level 1: Perangkat Daerah -->
                            <div class="perangkat-header" onclick="togglePerangkat(this)">
                                <span class="toggle-btn-1 collapsed">▼</span>
                                <div class="perangkat-name">
                                    <span class="perangkat-icon">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <span class="perangkat-label">{{ $perangkatGroup['perangkat']->nama }}</span>
                                </div>
                            </div>

                            <!-- Level 2 & 3 Container -->
                            <div class="units-container" data-perangkat-id="{{ $perangkatGroup['perangkat']->id }}">
                                @foreach($perangkatGroup['units'] as $unitGroup)
                                    <div>
                                        <!-- Level 2: Unit Organisasi -->
                                        <div class="unit-header" onclick="toggleUnit(event)">
                                            <span class="toggle-btn-2 collapsed">▼</span>
                                            <div class="unit-name">
                                                <span class="unit-icon">
                                                    <i class="fas fa-sitemap"></i>
                                                </span>
                                                <span class="unit-label">{{ $unitGroup['unit']->nama }}</span>
                                                <span class="unit-count">{{ count($unitGroup['jabatan']) }} Posisi</span>
                                            </div>
                                        </div>

                                        <!-- Level 3: Jabatan -->
                                        <div class="jabatan-container" data-unit-id="{{ $unitGroup['unit']->id }}">
                                            @foreach($unitGroup['jabatan'] as $jabatan)
                                                <div class="jabatan-item">
                                                    <span class="jabatan-icon">
                                                        <i class="fas fa-arrow-right"></i>
                                                    </span>
                                                    <div class="jabatan-content">
                                                        <span class="jabatan-kode">{{ $jabatan->kode }}</span>
                                                        <span class="jabatan-nama">{{ $jabatan->nama }}</span>
                                                    </div>
                                                    <div class="jabatan-stats">
                                                        <span class="stat-badge badge-b" title="Bezetting">{{ $jabatan->b }}</span>
                                                        <span class="stat-badge badge-k" title="Kebutuhan">{{ $jabatan->k }}</span>
                                                        <span class="stat-badge badge-gap {{ $jabatan->b > $jabatan->k ? 'badge-gap-positive' : ($jabatan->b < $jabatan->k ? 'badge-gap-negative' : 'badge-gap-zero') }}" title="Gap">
                                                            {{ $jabatan->b > $jabatan->k ? '+' : '' }}{{ $jabatan->b - $jabatan->k }}
                                                        </span>
                                                    </div>
                                                    <div class="jabatan-actions">
                                                        <a href="{{ route('jabatan.edit', $jabatan->id) }}" class="btn-action btn-edit" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete({{ $jabatan->id }})" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <p>Belum ada data Jabatan</p>
                    </div>
                @endif
            </div>

            <!-- Pagination Footer -->
            @if($perangkatDaerahs->count() > 0)
                <div class="pagination-footer">
                    {{ $perangkatDaerahs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Hidden Form for Delete -->
<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<script>
// Toggle Perangkat Daerah
function togglePerangkat(element) {
    const perangkatId = null; // Get from data attr if needed
    const toggleBtn = element.querySelector('.toggle-btn-1');
    const unitsContainer = element.nextElementSibling;
    
    unitsContainer.classList.toggle('show');
    toggleBtn.classList.toggle('collapsed');
}

// Toggle Unit Organisasi
function toggleUnit(event) {
    event.stopPropagation();
    const element = event.currentTarget;
    const toggleBtn = element.querySelector('.toggle-btn-2');
    const jabatanContainer = element.nextElementSibling;
    
    jabatanContainer.classList.toggle('show');
    toggleBtn.classList.toggle('collapsed');
}

// Change per page
function changePerPage(value) {
    const url = new URL(window.location);
    url.searchParams.set('per_page', value);
    url.searchParams.set('page', 1);
    window.location.href = url.toString();
}

// Confirm delete
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/jabatan/' + id;
        form.submit();
    }
}
</script>
@endsection