@extends('layouts.app')

@section('title', 'Master Unit Organisasi')

@section('css')
<style>
    /* Search Container */
    .search-container {
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 0 1 200px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 10px 35px 10px 14px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .search-box input:focus {
        outline: none;
        border-color: #0b58a6;
        box-shadow: 0 0 0 3px rgba(11, 88, 166, 0.1);
    }

    /* Clear Button */
    .search-clear-btn {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 18px;
        padding: 0;
        display: none;
        transition: all 0.2s ease;
    }

    .search-clear-btn:hover {
        color: #333;
    }

    .search-box input:not(:placeholder-shown) ~ .search-clear-btn {
        display: block;
    }

    .button-group {
        display: flex;
        gap: 8px;
    }

    .btn-toggle {
        padding: 9px 15px;
        border: none;
        border-radius: 6px;
        background-color: #0b58a6;
        color: white;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        min-width: 120px;
        justify-content: center;
    }

    .btn-toggle:hover {
        background-color: #0a4f94;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(11, 88, 166, 0.2);
    }

    /* OPD Header */
    .opd-header {
        background-color: #f8f9fa;
        border-left: 4px solid #ccc;
        color: #333;
        font-weight: 600;
        padding: 14px 16px;
        margin-top: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-radius: 6px;
        border: 1px solid #e0e0e0;
    }

    .opd-header:first-of-type {
        margin-top: 0;
    }

    .opd-header:hover {
        background-color: #eeeeee;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border-color: #0b58a6;
    }

    .opd-header:not(.collapsed) {
        border-left-color: #0b58a6;
    }

    .opd-header-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 14px;
    }

    .opd-header-title i {
        color: #0b58a6;
        width: 20px;
        text-align: center;
    }

    .opd-name {
        font-weight: 600;
        color: #0b2545;
    }

    .unit-count-badge {
        background: #e3f2fd;
        color: #0b58a6;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 400;
    }

    .toggle-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 20px;
        height: 20px;
        color: #0b58a6;
        transition: transform 0.3s ease;
        font-size: 12px;
        transform: rotate(-90deg);
    }

    .opd-header:not(.collapsed) .toggle-icon {
        transform: rotate(0deg);
    }

    /* Unit Details */
    .unit-details {
        display: none;
        padding: 12px 16px 16px 16px;
        background-color: #fafafa;
        margin-bottom: 10px;
        border-radius: 0 0 6px 6px;
        border: 1px solid #e0e0e0;
        border-top: none;
        transition: all 0.3s ease;
    }

    .unit-details:not(.hidden) {
        display: block;
    }

    /* Unit Row */
    .unit-row {
        display: grid;
        grid-template-columns: 40px 85px 1fr 250px auto;
        gap: 12px;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e8e8e8;
        transition: all 0.2s ease;
    }

    .unit-row:hover {
        background-color: #f0f7ff;
        padding-left: 8px;
        padding-right: 8px;
        margin: 0 -8px;
        border-radius: 4px;
    }

    .unit-row:last-child {
        border-bottom: none;
    }

    .unit-row.hidden-search {
        display: none !important;
    }

    .unit-no {
        font-weight: 400;
        color: #0b58a6;
        text-align: center;
        font-size: 13px;
    }

    .unit-kode {
        background: #e3f2fd;
        color: #0b58a6;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 400;
        font-size: 12px;
        text-align: center;
    }

    .unit-info {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .unit-nama {
        font-weight: 400;
        color: #333;
        font-size: 14px;
    }

    .unit-atasan {
        color: #666;
        font-size: 12px;
    }

    .unit-atasan-empty {
        color: #bbb;
    }

    .unit-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .btn-action {
        padding: 6px 10px;
        font-size: 12px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-edit {
        background-color: #ffc107;
        color: #333;
    }

    .btn-edit:hover {
        background-color: #ffb300;
        transform: translateY(-2px);
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background-color: #c82333;
        transform: translateY(-2px);
    }

    .no-data-message {
        padding: 16px;
        text-align: center;
        color: #999;
        font-size: 13px;
    }

    .no-data-message i {
        font-size: 20px;
        margin-bottom: 8px;
        opacity: 0.5;
    }

    .no-results {
        padding: 40px 20px;
        text-align: center;
        color: #999;
        font-size: 14px;
    }

    .no-results i {
        font-size: 48px;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .search-container {
            justify-content: flex-start;
            flex-direction: column;
        }

        .search-box {
            flex: 1 1 100%;
            min-width: 100%;
        }

        .unit-row {
            grid-template-columns: 1fr;
            gap: 8px;
        }

        .unit-actions {
            justify-content: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-sitemap"></i> Master Unit Organisasi</h5>
                <a href="{{ route('unit_organisasi.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Tambah UNOR
                </a>
            </div>
            <div class="card-body">
                @if($perangkatDaerah->count() > 0)
                    <!-- Search & Control Bar -->
                    <div class="search-container">
                        <div class="search-box">
                            <input type="text" id="searchInput" placeholder="Search . . ." />
                            <button class="search-clear-btn" onclick="clearSearch()" title="Clear">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="button-group">
                            <button class="btn-toggle" id="toggleBtn" onclick="toggleAll()">
                                <i class="fas fa-chevron-down" id="toggleIcon"></i>
                                <span id="toggleText">Expand</span>
                            </button>
                        </div>
                    </div>

                    <!-- OPD Container -->
                    <div id="opdContainer">
                        @foreach($perangkatDaerah as $opd)
                            @php
                                $unitCount = $opd->unitOrganisasi->count();
                            @endphp

                            <!-- OPD Header - COLLAPSED BY DEFAULT -->
                            <div class="opd-header collapsed" 
                                 onclick="toggleOPD(this)" 
                                 data-opd-id="{{ $opd->id }}"
                                 data-search-text="{{ strtolower($opd->nama) }}">
                                <div class="opd-header-title">
                                    <span class="toggle-icon">
                                        <i class="fas fa-chevron-down"></i>
                                    </span>
                                    <i class="fas fa-building"></i>
                                    <span class="opd-name">{{ $opd->nama }}</span>
                                    <span class="unit-count-badge">{{ $unitCount }} Unit</span>
                                </div>
                            </div>

                            <!-- Unit Details - HIDDEN BY DEFAULT -->
                            <div class="unit-details hidden" id="details-{{ $opd->id }}">
                                @if($unitCount > 0)
                                    @foreach($opd->unitOrganisasi as $key => $unit)
                                        <div class="unit-row" 
                                             data-search-text="{{ strtolower($unit->nama . ' ' . $unit->kode . ' ' . $opd->nama) }}"
                                             data-opd-id="{{ $opd->id }}">
                                            <div class="unit-no">{{ $key + 1 }}</div>
                                            <div class="unit-kode">{{ $unit->kode }}</div>
                                            <div class="unit-info">
                                                <div class="unit-nama">{{ $unit->nama }}</div>
                                                <div class="unit-atasan">
                                                    @if($unit->unor_atasan)
                                                        Atasan: {{ $unit->unor_atasan }}
                                                    @else
                                                        <span class="unit-atasan-empty">-</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="unit-actions">
                                                <a href="{{ route('unit_organisasi.edit', $unit->id) }}" 
                                                   class="btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('unit_organisasi.destroy', $unit->id) }}" 
                                                      method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete" 
                                                            onclick="confirmDelete(event)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="no-data-message">
                                        <i class="fas fa-inbox"></i>
                                        <p>Belum ada Unit Organisasi</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada data Perangkat Daerah
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
let isExpandedMode = false;

function toggleOPD(headerElement) {
    const opdId = headerElement.getAttribute('data-opd-id');
    const detailsElement = document.getElementById('details-' + opdId);
    
    headerElement.classList.toggle('collapsed');
    
    if (detailsElement.classList.contains('hidden')) {
        detailsElement.classList.remove('hidden');
    } else {
        detailsElement.classList.add('hidden');
    }
}

function toggleAll() {
    isExpandedMode = !isExpandedMode;
    const toggleBtn = document.getElementById('toggleBtn');
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleText = document.getElementById('toggleText');
    
    const headers = document.querySelectorAll('.opd-header');
    headers.forEach(header => {
        const opdId = header.getAttribute('data-opd-id');
        const detailsElement = document.getElementById('details-' + opdId);
        
        // Hanya toggle yang visible
        if (header.offsetParent !== null) {
            if (isExpandedMode) {
                header.classList.remove('collapsed');
                detailsElement.classList.remove('hidden');
            } else {
                header.classList.add('collapsed');
                detailsElement.classList.add('hidden');
            }
        }
    });
    
    // Update button appearance
    if (isExpandedMode) {
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
        toggleText.textContent = 'Collapse';
    } else {
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
        toggleText.textContent = 'Expand';
    }
}

function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    searchInput.focus();
    
    // Trigger search event
    searchInput.dispatchEvent(new Event('keyup'));
}

// Search Functionality - IMPROVED
const searchInput = document.getElementById('searchInput');
searchInput.addEventListener('keyup', function(e) {
    const searchText = e.target.value.toLowerCase().trim();
    const container = document.getElementById('opdContainer');
    const headers = document.querySelectorAll('.opd-header');
    let hasResults = false;

    if (searchText === '') {
        // Clear search - tampilkan semua dan collapse
        headers.forEach(header => {
            header.style.display = '';
            const opdId = header.getAttribute('data-opd-id');
            const detailsElement = document.getElementById('details-' + opdId);
            const unitRows = detailsElement.querySelectorAll('.unit-row');
            
            header.classList.add('collapsed');
            detailsElement.classList.add('hidden');
            unitRows.forEach(row => row.classList.remove('hidden-search'));
            detailsElement.style.display = '';
        });
        
        isExpandedMode = false;
        updateToggleButton();
        
        let noResults = document.getElementById('noResults');
        if (noResults) noResults.remove();
        return;
    }

    // Proses search
    headers.forEach(header => {
        const opdId = header.getAttribute('data-opd-id');
        const headerSearchText = header.getAttribute('data-search-text');
        const detailsElement = document.getElementById('details-' + opdId);
        const unitRows = detailsElement.querySelectorAll('.unit-row');
        
        let headerMatches = headerSearchText.includes(searchText);
        let visibleUnits = 0;

        // Check unit rows - SEMUA ditampilkan jika match
        unitRows.forEach(row => {
            const rowSearchText = row.getAttribute('data-search-text');
            if (rowSearchText.includes(searchText)) {
                row.classList.remove('hidden-search');
                visibleUnits++;
            } else {
                row.classList.add('hidden-search');
            }
        });

        // Show/hide header dan details
        if (headerMatches || visibleUnits > 0) {
            header.style.display = '';
            detailsElement.style.display = '';
            header.classList.remove('collapsed');
            detailsElement.classList.remove('hidden');
            hasResults = true;
        } else {
            header.style.display = 'none';
            detailsElement.style.display = 'none';
        }
    });

    // Show/hide "no results" message
    let noResults = document.getElementById('noResults');
    if (!hasResults && searchText !== '') {
        if (!noResults) {
            noResults = document.createElement('div');
            noResults.id = 'noResults';
            noResults.className = 'no-results';
            noResults.innerHTML = '<i class="fas fa-search"></i><p>Tidak ada hasil yang cocok</p>';
            container.appendChild(noResults);
        }
    } else {
        if (noResults) noResults.remove();
    }
});

function updateToggleButton() {
    const toggleIcon = document.getElementById('toggleIcon');
    const toggleText = document.getElementById('toggleText');
    
    if (isExpandedMode) {
        toggleIcon.classList.remove('fa-chevron-down');
        toggleIcon.classList.add('fa-chevron-up');
        toggleText.textContent = 'Collapse';
    } else {
        toggleIcon.classList.remove('fa-chevron-up');
        toggleIcon.classList.add('fa-chevron-down');
        toggleText.textContent = 'Expand';
    }
}
</script>
@endsection