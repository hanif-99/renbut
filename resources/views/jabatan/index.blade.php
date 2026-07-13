@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('css')
<style>
  /* ===== RESET ===== */
  * {
    margin: 0;
    padding: 0;
  }

  /* ===== CARD ===== */
  .card {
    border: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-radius: 6px;
    overflow: hidden;
  }

  /* ===== CARD HEADER (sama dengan Unit Organisasi) ===== */
  .card-header {
    background: linear-gradient(135deg, #0b2545 0%, #0b58a6 100%);
    padding: 12px 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .card-header h5 {
    color: #fff;
    font-weight: 500;
    font-size: 16px;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .card-header i {
    margin-right: 4px;
  }

  /* ===== BUTTON PRIMARY ===== */
  .btn-primary {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background-color 0.2s ease;
    white-space: nowrap;
  }

  .btn-primary:hover {
    background-color: #0056b3;
    color: white;
    text-decoration: none;
  }

  /* ===== CONTROL BAR ===== */
  .control-bar {
    padding: 12px 16px;
    background-color: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
  }

  .control-info {
    font-size: 13px;
    color: #6b7280;
    font-weight: 400;
  }

  .per-page-control {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .per-page-control label {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
    font-weight: 400;
  }

  .per-page-control select {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 4px;
    font-size: 13px;
    cursor: pointer;
    background-color: #fff;
  }

  /* ===== HIERARCHY CONTAINER ===== */
  .hierarchy-container {
    padding: 12px 16px;
    background-color: #ffffff;
  }

  .hierarchy-level-1 {
    margin-bottom: 12px;
  }

  /* ===== LEVEL 1: PERANGKAT DAERAH ===== */
  .perangkat-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background-color: #f8f9fa;
    border-radius: 6px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    user-select: none;
    font-weight: 400;
    border: 1px solid #e8eaed;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  }

  .perangkat-header:hover {
    background-color: #f0f2f5;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .toggle-btn-1 {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    cursor: pointer;
    transition: transform 0.2s ease;
    color: #0b58a6;
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
    font-size: 13px;
  }

  .perangkat-icon {
    font-size: 14px;
    color: #0b58a6;
    flex-shrink: 0;
  }

  .perangkat-label {
    font-weight: 400;
    color: #2c3e50;
  }

  .units-container {
    display: none;
    margin-left: 20px;
    margin-top: 8px;
    padding-left: 12px;
  }

  .units-container.show {
    display: block;
  }

  /* ===== LEVEL 2: UNIT ORGANISASI ===== */
  .unit-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: #fafbfc;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    user-select: none;
    font-weight: 400;
    margin-bottom: 8px;
    border: 1px solid #e5e7eb;
    font-size: 13px;
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
    color: #0b58a6;
    font-size: 10px;
    font-weight: bold;
    flex-shrink: 0;
  }

  .toggle-btn-2.collapsed {
    transform: rotate(-90deg);
  }

  .unit-icon {
    font-size: 13px;
    color: #0b58a6;
    flex-shrink: 0;
  }

  .unit-name {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .unit-label {
    font-weight: 400;
    color: #2c3e50;
    font-size: 13px;
  }

  .unit-count {
    background-color: #e3f2fd;
    color: #0b58a6;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 400;
    flex-shrink: 0;
  }

  .jabatan-container {
    display: none;
    margin-left: 20px;
    margin-top: 6px;
    padding-left: 12px;
  }

  .jabatan-container.show {
    display: block;
  }

  /* ===== LEVEL 3: JABATAN ===== */
  .jabatan-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    margin-bottom: 6px;
    transition: background-color 0.2s ease;
    font-size: 13px;
  }

  .jabatan-item:hover {
    background-color: #fafbfc;
  }

  .jabatan-icon {
    font-size: 12px;
    color: #9ca3af;
    flex-shrink: 0;
  }

  .jabatan-content {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
  }

  .jabatan-kode {
    padding: 4px 8px;
    background-color: #eaf6ff;
    color: #0b58a6;
    border-radius: 3px;
    font-weight: 400;
    font-size: 12px;
    min-width: 60px;
    text-align: center;
    flex-shrink: 0;
  }

  .jabatan-nama {
    flex: 1;
    color: #2c3e50;
    min-width: 200px;
    font-weight: 400;
  }

  .jabatan-stats {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }

  .stat-badge {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 400;
    white-space: nowrap;
    min-width: 35px;
    text-align: center;
  }

  .badge-b {
    background-color: #e3f2fd;
    color: #0b58a6;
  }

  .badge-k {
    background-color: #fff3e0;
    color: #e65100;
  }

  .badge-gap-positive {
    background-color: #ffebee;
    color: #c62828;
  }

  .badge-gap-negative {
    background-color: #e8f5e9;
    color: #2e7d32;
  }

  .badge-gap-zero {
    background-color: #f5f5f5;
    color: #616161;
  }

  .jabatan-actions {
    display: flex;
    gap: 4px;
    flex-shrink: 0;
  }

  .btn-action {
    padding: 6px 10px;
    border: none;
    border-radius: 3px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    min-width: 32px;
    height: 32px;
  }

  .btn-edit {
    background-color: #ffc107;
    color: #000;
  }

  .btn-edit:hover {
    background-color: #ffb300;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.2);
  }

  .btn-delete {
    background-color: #dc3545;
    color: #fff;
  }

  .btn-delete:hover {
    background-color: #c82333;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
  }

  /* ===== EMPTY STATE ===== */
  .empty-state {
    padding: 40px 16px;
    text-align: center;
    color: #7f8c8d;
  }

  .empty-state-icon {
    font-size: 28px;
    margin-bottom: 8px;
    color: #bdc3c7;
  }

  .empty-state p {
    font-size: 13px;
    margin: 0;
  }

  /* ===== PAGINATION ===== */
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
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 3px;
    font-size: 12px;
    color: #0b58a6;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .pagination-footer a:hover {
    background-color: #f0f7ff;
  }

  .pagination-footer .active span {
    background-color: #0b58a6;
    color: white;
    border-color: #0b58a6;
  }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 768px) {
    .card-header {
      flex-direction: column;
      align-items: flex-start;
    }

    .control-bar {
      flex-direction: column;
      align-items: flex-start;
    }

    .jabatan-content {
      flex-direction: column;
      align-items: flex-start;
      gap: 4px;
    }

    .jabatan-actions {
      width: 100%;
      justify-content: flex-start;
    }
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <h5><i class="fas fa-briefcase"></i> Master Jabatan</h5>
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
                            <span class="stat-badge badge-gap {{ $jabatan->b > $jabatan->k ? 'badge-gap-positive' : ($jabatan->b < $jabatan->k ? 'badge-gap-negative' : 'badge-gap-zero') }}">
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
