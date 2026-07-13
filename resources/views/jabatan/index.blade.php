@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('css')
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  .card {
    border: none;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    overflow: hidden;
  }

  .card-header {
    background: linear-gradient(135deg, #0b2545 0%, #0b58a6 100%);
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
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

  .btn-primary {
    background-color: #fff;
    color: #0b58a6;
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
    transition: all 0.2s ease;
    white-space: nowrap;
  }

  .btn-primary:hover {
    background-color: #f0f0f0;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .search-bar {
    padding: 12px 16px;
    background-color: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    gap: 10px;
    justify-content: flex-end;
  }

  .search-box { 
    flex: 0 1 200px;
    max-width: 260px;
    position: relative; 
  }

  .search-box input { 
    width: 100%; 
    padding: 8px 30px 8px 12px;
    border: 1px solid #ddd; 
    border-radius: 6px; 
    font-size: 14px; 
    background-color: #fff;
    transition: border-color 0.2s ease;
  }

  .search-box input:focus { 
    outline: none; 
    border-color: #0b58a6; 
    box-shadow: 0 0 0 3px rgba(11, 88, 166, 0.06); 
  }

  .search-clear-btn { 
    position: absolute; 
    right: 8px; 
    top: 50%; 
    transform: translateY(-50%); 
    background: none; 
    border: none; 
    color: #bbb; 
    cursor: pointer; 
    font-size: 14px; 
    padding: 0; 
    display: none;
    transition: color 0.2s ease; 
  }

  .search-clear-btn:hover {
    color: #999;
  }

  .search-box input:not(:placeholder-shown) ~ .search-clear-btn { 
    display: block; 
  }

  .main-content {
    padding: 16px;
    background-color: #ffffff;
    min-height: 300px;
  }

  .perangkat-section {
    margin-bottom: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    background-color: #ffffff;
  }

  .perangkat-header {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 14px;
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    font-weight: 500;
    color: #2c3e50;
    font-size: 13px;
    border-bottom: 1px solid #e5e7eb;
  }

  .perangkat-header:hover { background-color: #f0f2f5; }
  .perangkat-header.open { background-color: #eef6ff; }

  .toggle-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 16px;
    height: 16px;
    color: #0b58a6;
    font-size: 9px;
    font-weight: bold;
    transition: transform 0.2s ease;
    flex-shrink: 0;
  }

  .toggle-icon.collapsed { transform: rotate(-90deg); }

  .perangkat-info {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
  }

  .perangkat-icon {
    font-size: 14px;
    color: #0b58a6;
  }

  .perangkat-name { flex: 1; }

  .perangkat-badge {
    background-color: #e3f2fd;
    color: #0b58a6;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    flex-shrink: 0;
  }

  .unit-section {
    display: none;
    padding: 0;
    background-color: #ffffff;
  }

  .unit-section.open { display: block; }

  .unit-group {
    border-bottom: 1px solid #f0f0f0;
  }

  .unit-group:last-child { border-bottom: none; }

  .unit-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px 10px 30px;
    background-color: #fafbfc;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    font-weight: 400;
    color: #2c3e50;
    font-size: 12px;
  }

  .unit-header:hover { background-color: #f3f4f6; }
  .unit-header.open { background-color: #f0f7ff; }

  .toggle-icon-2 {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 14px;
    height: 14px;
    color: #0b58a6;
    font-size: 8px;
    font-weight: bold;
    transition: transform 0.2s ease;
    flex-shrink: 0;
  }

  .toggle-icon-2.collapsed { transform: rotate(-90deg); }

  .unit-icon {
    font-size: 12px;
    color: #0b58a6;
  }

  .unit-name { flex: 1; }

  .unit-count {
    background-color: #f3f4f6;
    color: #0b58a6;
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 10px;
    flex-shrink: 0;
  }

  .jabatan-list {
    display: none;
    padding: 0;
    background-color: #ffffff;
    margin-left: 30px;
  }

  .jabatan-list.open { display: block; }

  .jabatan-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 10px 8px 14px;
    border-bottom: 1px solid #fafbfc;
    transition: all 0.2s ease;
    font-size: 12px;
  }

  .jabatan-item:last-child { border-bottom: none; }
  .jabatan-item:hover { background-color: #f9fafb; }

  .jabatan-kode {
    padding: 2px 6px;
    background-color: #eaf6ff;
    color: #0b58a6;
    border-radius: 3px;
    font-weight: 500;
    font-size: 11px;
    min-width: 50px;
    text-align: center;
    flex-shrink: 0;
  }

  .jabatan-nama {
    flex: 1;
    color: #2c3e50;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .jabatan-stats {
    display: flex;
    gap: 4px;
    flex-shrink: 0;
  }

  .stat-badge {
    padding: 2px 5px;
    border-radius: 3px;
    font-size: 10px;
    font-weight: 500;
    min-width: 30px;
    text-align: center;
  }

  .badge-b { background-color: #e3f2fd; color: #0b58a6; }
  .badge-k { background-color: #fff3e0; color: #e65100; }
  .badge-gap-pos { background-color: #ffebee; color: #c62828; }
  .badge-gap-neg { background-color: #e8f5e9; color: #2e7d32; }
  .badge-gap-zero { background-color: #f5f5f5; color: #616161; }

  .jabatan-actions {
    display: flex;
    gap: 3px;
    flex-shrink: 0;
  }

  .btn-action {
    padding: 3px 5px;
    border: none;
    border-radius: 3px;
    font-size: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
  }

  .btn-edit { background-color: #ffc107; color: #000; }
  .btn-edit:hover { background-color: #ffb300; }
  .btn-delete { background-color: #dc3545; color: #fff; }
  .btn-delete:hover { background-color: #c82333; }

  .empty-state {
    padding: 60px 20px;
    text-align: center;
    color: #7f8c8d;
  }

  .empty-state-icon {
    font-size: 32px;
    margin-bottom: 12px;
    color: #bdc3c7;
  }

  .info-bar {
    padding: 12px 16px;
    background-color: #f9fafb;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 20px;
    font-size: 12px;
    color: #6b7280;
    flex-wrap: wrap;
  }

  .info-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .info-value {
    font-weight: 600;
    color: #0b58a6;
  }

  .search-results-view {
    display: none;
  }

  .search-results-view.show {
    display: block;
  }

  @media (max-width: 768px) {
    .card-header { flex-direction: column; align-items: flex-start; }
    .search-bar { justify-content: flex-start; }
    .search-box { flex: 1; max-width: 100%; }
    .jabatan-item { flex-wrap: wrap; }
    .jabatan-actions { width: 100%; margin-top: 4px; }
    .info-bar { gap: 10px; }
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <!-- Header -->
      <div class="card-header">
        <h5><i class="fas fa-briefcase"></i> Master Jabatan</h5>
        <a href="{{ route('jabatan.create') }}" class="btn-primary">
          <i class="fas fa-plus"></i> Tambah Jabatan
        </a>
      </div>

      <!-- Search Bar -->
      <div class="search-bar">
        <div class="search-box">
          <input type="text" id="searchInput" placeholder="Cari jabatan..." autocomplete="off" />
          <button class="search-clear-btn" onclick="clearSearch()" title="Hapus">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>

      <!-- Normal View -->
      <div id="normalView" class="main-content">
        @if(count($groupedData) > 0)
          @foreach($groupedData as $pd)
            <div class="perangkat-section">
              <div class="perangkat-header" onclick="togglePerangkat(this)">
                <span class="toggle-icon collapsed">▼</span>
                <div class="perangkat-info">
                  <span class="perangkat-icon"><i class="fas fa-building"></i></span>
                  <span class="perangkat-name">{{ $pd['perangkat_nama'] }}</span>
                  <span class="perangkat-badge">{{ $pd['unit_count'] }} Unit</span>
                </div>
              </div>

              <div class="unit-section">
                @foreach($pd['units'] as $unit)
                  <div class="unit-group">
                    <div class="unit-header" onclick="toggleUnit(event)">
                      <span class="toggle-icon-2 collapsed">▼</span>
                      <span class="unit-icon"><i class="fas fa-sitemap"></i></span>
                      <span class="unit-name">{{ $unit['unit_nama'] }}</span>
                      <span class="unit-count">{{ $unit['jabatan_count'] }}</span>
                    </div>

                    <div class="jabatan-list">
                      @foreach($unit['jabatan'] as $jab)
                        <div class="jabatan-item">
                          <span class="jabatan-kode">{{ $jab->kode }}</span>
                          <span class="jabatan-nama" title="{{ $jab->nama }}">{{ $jab->nama }}</span>
                          <div class="jabatan-stats">
                            <span class="stat-badge badge-b">B:{{ $jab->b }}</span>
                            <span class="stat-badge badge-k">K:{{ $jab->k }}</span>
                            <span class="stat-badge {{ $jab->b > $jab->k ? 'badge-gap-pos' : ($jab->b < $jab->k ? 'badge-gap-neg' : 'badge-gap-zero') }}">
                              {{ $jab->b > $jab->k ? '+' : '' }}{{ $jab->b - $jab->k }}
                            </span>
                          </div>
                          <div class="jabatan-actions">
                            <a href="{{ route('jabatan.edit', $jab->id) }}" class="btn-action btn-edit" title="Edit">
                              <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn-action btn-delete" onclick="confirmDelete({{ $jab->id }})" title="Hapus">
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
            <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
            <p>Belum ada data Jabatan</p>
          </div>
        @endif
      </div>

      <!-- Search Results View -->
      <div id="searchResultsView" class="search-results-view main-content"></div>

      <!-- Info Bar -->
      <div class="info-bar">
        <div class="info-item">
          <i class="fas fa-briefcase" style="color: #0b58a6;"></i>
          <span>Total Jabatan: <span class="info-value">{{ $totalJabatan ?? 0 }}</span></span>
        </div>
        <div class="info-item">
          <i class="fas fa-sitemap" style="color: #0b58a6;"></i>
          <span>Total Unit: <span class="info-value">{{ $totalUnit ?? 0 }}</span></span>
        </div>
        <div class="info-item">
          <i class="fas fa-building" style="color: #0b58a6;"></i>
          <span>Total Perangkat: <span class="info-value">{{ $totalPerangkat ?? 0 }}</span></span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Hidden Form for Delete -->
<form id="deleteForm" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
</form>

<script>
  const searchUrl = "{{ url('/jabatan/search') }}";
  const jabatanBaseUrl = "{{ url('jabatan') }}";
  let searchDebounceTimer = null;

  function escapeHtml(unsafe) {
    if (!unsafe) return '';
    return String(unsafe).replace(/[&<>"'`=\/]/g, s => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;'
    })[s]);
  }

  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('input', function(e) {
        const q = e.target.value.trim();
        if (q === '') {
          clearSearch();
          return;
        }
        if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
        searchDebounceTimer = setTimeout(() => {
          if (q.length >= 1) performSearch(q);
        }, 300);
      });
    }
  });

  async function performSearch(query) {
    const resultsView = document.getElementById('searchResultsView');
    const normalView = document.getElementById('normalView');
    
    resultsView.innerHTML = '<div style="text-align: center; padding: 60px 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #0b58a6;"></i><p style="margin-top: 12px; color: #999;">Mencari...</p></div>';
    resultsView.classList.add('show');
    normalView.style.display = 'none';

    try {
      const res = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, { 
        headers: { 'Accept': 'application/json' }
      });
      const json = await res.json();
      renderSearchResults(resultsView, json.data || {});
    } catch (e) {
      resultsView.innerHTML = '<div style="text-align: center; padding: 60px 20px; color: #7f8c8d;"><i class="fas fa-exclamation-circle" style="font-size: 24px; margin-bottom: 12px;"></i><p>Gagal melakukan pencarian</p></div>';
    }
  }

  function renderSearchResults(container, data) {
    if (Object.keys(data).length === 0) {
      container.innerHTML = '<div style="text-align: center; padding: 60px 20px; color: #7f8c8d;"><i class="fas fa-search" style="font-size: 24px; margin-bottom: 12px;"></i><p>Tidak ada hasil pencarian</p></div>';
      return;
    }

    let html = '';
    Object.keys(data).forEach(pdKey => {
      const pdData = data[pdKey];
      const pdName = pdData.perangkat_nama;
      const unitsObj = pdData.units;

      html += `<div class="perangkat-section">
                 <div class="perangkat-header open" onclick="togglePerangkat(this)">
                   <span class="toggle-icon">▼</span>
                   <div class="perangkat-info">
                     <span class="perangkat-icon"><i class="fas fa-building"></i></span>
                     <span class="perangkat-name">${escapeHtml(pdName)}</span>
                     <span class="perangkat-badge">${Object.keys(unitsObj).length} Unit</span>
                   </div>
                 </div>
                 <div class="unit-section open">`;

      Object.keys(unitsObj).forEach(uoKey => {
        const unitData = unitsObj[uoKey];
        const unitName = unitData.unit_nama;
        const jabatanList = unitData.jabatan;

        html += `<div class="unit-group">
                   <div class="unit-header open" onclick="toggleUnit(event)">
                     <span class="toggle-icon-2">▼</span>
                     <span class="unit-icon"><i class="fas fa-sitemap"></i></span>
                     <span class="unit-name">${escapeHtml(unitName)}</span>
                     <span class="unit-count">${jabatanList.length}</span>
                   </div>
                   <div class="jabatan-list open">`;

        jabatanList.forEach(jab => {
          const gap = jab.b - jab.k;
          const gapClass = gap > 0 ? 'badge-gap-pos' : (gap < 0 ? 'badge-gap-neg' : 'badge-gap-zero');
          html += `<div class="jabatan-item">
                     <span class="jabatan-kode">${escapeHtml(jab.kode)}</span>
                     <span class="jabatan-nama" title="${escapeHtml(jab.nama)}">${escapeHtml(jab.nama)}</span>
                     <div class="jabatan-stats">
                       <span class="stat-badge badge-b">B:${jab.b}</span>
                       <span class="stat-badge badge-k">K:${jab.k}</span>
                       <span class="stat-badge ${gapClass}">${gap > 0 ? '+' : ''}${gap}</span>
                     </div>
                     <div class="jabatan-actions">
                       <a href="${jabatanBaseUrl}/${jab.id}/edit" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
                       <button type="button" class="btn-action btn-delete" onclick="confirmDelete(${jab.id})"><i class="fas fa-trash"></i></button>
                     </div>
                   </div>`;
        });

        html += `</div></div>`;
      });

      html += `</div></div>`;
    });

    container.innerHTML = html;
  }

  function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) searchInput.value = '';
    if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
    const resultsView = document.getElementById('searchResultsView');
    const normalView = document.getElementById('normalView');
    resultsView.classList.remove('show');
    resultsView.innerHTML = '';
    normalView.style.display = 'block';
  }

  function togglePerangkat(element) {
    const icon = element.querySelector('.toggle-icon');
    const section = element.nextElementSibling;
    if (!section) return;
    
    const isOpen = element.classList.contains('open');
    element.classList.toggle('open', !isOpen);
    section.classList.toggle('open', !isOpen);
    icon.classList.toggle('collapsed', isOpen);
  }

  function toggleUnit(event) {
    event.stopPropagation();
    const element = event.currentTarget;
    const icon = element.querySelector('.toggle-icon-2');
    const jabatanList = element.nextElementSibling;
    if (!jabatanList) return;
    
    const isOpen = element.classList.contains('open');
    element.classList.toggle('open', !isOpen);
    jabatanList.classList.toggle('open', !isOpen);
    icon.classList.toggle('collapsed', isOpen);
  }

  function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
      const form = document.getElementById('deleteForm');
      form.action = '/jabatan/' + id;
      form.submit();
    }
  }
</script>
@endsection