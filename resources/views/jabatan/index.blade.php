@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('css')
<style>
  .search-container { 
    margin-bottom: 10px;
    display: flex; 
    gap: 10px; 
    align-items: center; 
    justify-content: flex-end; 
    flex-wrap: wrap; 
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

  .search-clear-btn:hover { color: #999; }

  .search-box input:not(:placeholder-shown) ~ .search-clear-btn { display: block; }

  /* PD HEADER */
  .pd-header { 
    background: #f8f9fa;
    color: #2c3e50; 
    font-weight: 400;
    padding: 10px 12px;
    margin-top: 8px;
    cursor: pointer; 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    transition: all 0.12s ease;
  }

  .pd-header:hover { background: #f0f2f5; }

  .pd-header-title { 
    display: flex; 
    align-items: center; 
    gap: 8px;
    font-size: 14px; 
    flex: 1;
  }

  .pd-header i:first-child { font-size: 14px; color: #0b58a6; flex-shrink: 0; }

  .pd-name { 
    font-weight: 400; 
    color: #2c3e50; 
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - 120px);
  }

  .unit-count-badge { 
    background: #e3f2fd;
    color: #0b58a6; 
    padding: 4px 8px;
    border-radius: 12px; 
    font-size: 11px; 
    font-weight: 400;
    margin-left: 6px;
    flex-shrink: 0;
  }

  .toggle-icon { 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    width: 18px; 
    height: 18px; 
    color: #0b58a6; 
    font-size: 10px; 
    transform: rotate(-90deg);
    flex-shrink: 0;
    transition: transform 0.12s ease;
    margin-right: 4px;
  }

  .pd-header.open .toggle-icon { transform: rotate(0deg); }

  .pd-details { 
    display: none; 
    padding: 0; 
    background-color: #fff; 
    margin-bottom: 6px; 
    border-radius: 0 0 6px 6px; 
    border: 1px solid #e8eaed;
    border-top: none;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  }

  .pd-details.open { display: block; }

  /* UNIT HEADER - NO CODE HERE */
  .unit-list { display: flex; flex-direction: column; }

  .unit-header {
    padding: 10px 12px;
    background-color: #fafbfc;
    font-size: 13px;
    color: #2c3e50;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.12s ease;
    user-select: none;
    border-bottom: 1px solid #f0f0f0;
  }

  .unit-header:hover { background-color: #f0f2f5; }

  .unit-header-title {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
    min-width: 0;
  }

  .unit-header-title .icon {
    font-size: 12px;
    color: #0b58a6;
    flex-shrink: 0;
  }

  .unit-header-title .name {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex: 1;
  }

  .unit-badge {
    background: #e8f5e9;
    color: #2e7d32;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 500;
    flex-shrink: 0;
    margin-left: 8px;
  }

  .unit-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    color: #666;
    font-size: 10px;
    transform: rotate(-90deg);
    transition: transform 0.12s ease;
    flex-shrink: 0;
    margin-left: 8px;
  }

  .unit-header.expanded .unit-toggle { transform: rotate(0deg); }

  .unit-body {
    display: none;
    padding: 0;
    background-color: #fff;
  }

  .unit-body.expanded { display: block; }

  /* JABATAN ROW - CODE HERE */
  .jabatan-list { 
    display: flex;
    flex-direction: column;
    padding: 0 12px;
  }

  .jabatan-row { 
    display: grid; 
    grid-template-columns: 70px 1fr 100px auto;
    gap: 12px; 
    align-items: center; 
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 12px;
  }

  .jabatan-row:last-child { border-bottom: none; }

  .jabatan-kode { 
    background: #eaf6ff;
    color: #0b58a6; 
    padding: 5px 8px; 
    border-radius: 4px; 
    font-weight: 600; 
    font-size: 11px; 
    text-align: center;
    min-width: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 24px;
  }

  .jabatan-nama { 
    font-weight: 400; 
    color: #2c3e50; 
    font-size: 12px;
    line-height: 1.3;
    word-break: break-word;
  }

  .jabatan-stats {
    display: flex;
    gap: 6px;
    justify-content: flex-end;
  }

  .stat-badge {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    font-weight: 600;
    min-width: 35px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 24px;
  }

  .badge-b { background-color: #e3f2fd; color: #0b58a6; }
  .badge-k { background-color: #e8f5e9; color: #2e7d32; }
  .badge-gap-neg { background-color: #f7e2e5; color: #c62828; }
  .badge-gap-pos { background-color: #fff3e0; color: #e65100; }
  .badge-gap-zero { background-color: #f0f0f0; color: #616161; }

  .jabatan-actions { 
    display: flex; 
    gap: 6px; 
    justify-content: flex-end;
  }

  .btn-action {
    border: none;
    cursor: pointer;
    padding: 4px 8px;
    font-size: 11px;
    border-radius: 3px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.12s ease;
    height: 24px;
    min-width: 24px;
  }

  .btn-edit { background-color: #ffc107; color: #000; }
  .btn-edit:hover { background-color: #ffb300; }
  .btn-delete { background-color: #dc3545; color: #fff; }
  .btn-delete:hover { background-color: #c82333; }

  /* SEARCH RESULTS */
  .search-results { display: none; }
  .search-results.show { display: block; }

  .pd-group { margin-bottom: 12px; }
  .pd-group .pd-header-search { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    gap: 8px; 
    padding: 10px 12px; 
    background: #f8f9fa;
    border: 1px solid #e8eaed; 
    border-radius: 6px;
    border-bottom: none;
  }

  .pd-results { 
    background: #fff; 
    border: 1px solid #e8eaed; 
    border-radius: 0 0 6px 6px; 
  }

  .no-data-message {
    text-align: center;
    padding: 28px 12px;
    color: #7f8c8d;
  }

  .no-data-message i { font-size: 28px; margin-bottom: 8px; display: block; color: #bdc3c7; }
  .no-data-message p { font-size: 13px; margin: 0; }

  .card-header { background: linear-gradient(135deg, #0b2545 0%, #0b58a6 100%); }
  .card-header h5 { color: #fff; font-weight: 500; font-size: 16px; }

  @media (max-width: 768px) {
    .jabatan-row { grid-template-columns: 1fr; gap: 8px; }
    .jabatan-actions { justify-content: flex-start; }
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center"> 
        <h5 class="mb-0"><i class="fas fa-briefcase"></i> Master Jabatan</h5>
        <a href="{{ route('jabatan.create') }}" class="btn btn-sm btn-light">
          <i class="fas fa-plus"></i> Tambah Data
        </a>
      </div>

      <div class="card-body">
        @if($perangkatDaerah->count() > 0)
          <div class="search-container">
            <div class="search-box">
              <input type="text" id="searchInput" placeholder="Cari jabatan..." autocomplete="off" />
              <button class="search-clear-btn" onclick="clearSearch()">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div id="normalView" style="padding: 0 16px; margin-top: -4px;">
            @foreach($perangkatDaerah as $pd)
              <div class="pd-header collapsed" data-pd-id="{{ $pd->id }}" data-has-units="{{ $pd->unit_count > 0 ? 'true' : 'false' }}">
                <div class="pd-header-title">
                  <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                  <i class="fas fa-building"></i>
                  <span class="pd-name">{{ $pd->nama }}</span>
                  <span class="unit-count-badge">{{ $pd->unit_count }} Unit</span>
                </div>
              </div>
              <div class="pd-details" id="details-{{ $pd->id }}" data-loaded="0"></div>
            @endforeach
          </div>

          <div id="searchResults" class="search-results"></div>
        @else
          <div class="alert alert-info" style="margin: 16px;">
            <i class="fas fa-info-circle"></i> Belum ada data Perangkat Daerah
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<form id="deleteForm" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
</form>

@endsection

@section('js')
<script>
const jabatanBaseUrl = "{{ url('jabatan') }}";
const getUnitsUrl = "{{ route('jabatan.getUnits', ':id') }}";
const getJabatanUrl = "{{ route('jabatan.getByUnitJson', ':id') }}";
const searchUrl = "{{ route('jabatan.search') }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

let searchDebounceTimer = null;
let isSearching = false;
let originalView = '';
const SHOW_FIRST = 8;

function escapeHtml(unsafe) {
  if (unsafe === null || unsafe === undefined) return '';
  return String(unsafe).replace(/[&<>"'`=\/]/g, s => 
    ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' })[s]
  );
}

document.addEventListener('DOMContentLoaded', () => {
  const normalView = document.getElementById('normalView');
  if (normalView) {
    originalView = normalView.innerHTML;
  }

  document.querySelectorAll('.pd-header').forEach(header => {
    header.addEventListener('click', function() {
      togglePD(this);
    });
  });

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
        if (q.length > 0) {
          serverSearch(q);
        }
      }, 500);
    });
  }
});

async function togglePD(headerElement) {
  if (!headerElement) return;
  
  const pdId = headerElement.getAttribute('data-pd-id');
  const hasUnits = headerElement.getAttribute('data-has-units') === 'true';
  const detailsElement = document.getElementById('details-' + pdId);
  
  if (!detailsElement) return;
  
  const isOpen = headerElement.classList.contains('open');

  if (!isOpen) {
    if (!hasUnits) {
      detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-inbox"></i><p>Belum ada Unit Organisasi</p></div>';
      detailsElement.classList.add('open');
      headerElement.classList.add('open');
      detailsElement.style.display = 'block';
      return;
    }

    if (detailsElement.dataset.loaded === '0') {
      detailsElement.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 26px; color: #0b58a6;"></i></div>';
      detailsElement.classList.add('open');
      detailsElement.style.display = 'block';
      try {
        await loadUnits(pdId);
      } catch (e) {
        console.error(e);
      }
    }
    headerElement.classList.add('open');
    detailsElement.classList.add('open');
    detailsElement.style.display = 'block';
  } else {
    headerElement.classList.remove('open');
    detailsElement.classList.remove('open');
    detailsElement.style.display = 'none';
  }
}

async function loadUnits(pdId) {
  const detailsElement = document.getElementById('details-' + pdId);
  if (!detailsElement) return;
  
  try {
    const url = getUnitsUrl.replace(':id', pdId);
    const res = await fetch(url, { 
      headers: { 'Accept': 'application/json' }, 
      credentials: 'same-origin' 
    });
    if (!res.ok) {
      detailsElement.innerHTML = `<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data</p></div>`;
      detailsElement.dataset.loaded = '0';
      return;
    }
    const json = await res.json();
    renderUnits(detailsElement, json.data || []);
  } catch (e) {
    console.error('Error loadUnits:', e);
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data</p></div>';
    detailsElement.dataset.loaded = '0';
  }
}

function renderUnits(detailsElement, units) {
  if (!units || units.length === 0) {
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-inbox"></i><p>Belum ada Unit Organisasi</p></div>';
    detailsElement.dataset.loaded = '1';
    return;
  }

  let html = '<div class="unit-list">';
  units.forEach((unit) => {
    const unitBodyId = `unit-body-${unit.id}`;
    html += `
      <div class="unit-header" onclick="toggleUnit(event, ${unit.id})">
        <div class="unit-header-title">
          <i class="icon fas fa-folder"></i>
          <span class="name">${escapeHtml(unit.nama)}</span>
        </div>
        <div>
          <span class="unit-badge">${unit.jabatan_count} Jabatan</span>
          <span class="unit-toggle"><i class="fas fa-chevron-down"></i></span>
        </div>
      </div>
      <div class="unit-body" id="${unitBodyId}" data-loaded="0"></div>
    `;
  });
  html += '</div>';

  detailsElement.innerHTML = html;
  detailsElement.dataset.loaded = '1';
}

async function toggleUnit(event, unitId) {
  event.stopPropagation();
  const unitBody = document.getElementById(`unit-body-${unitId}`);
  const header = unitBody?.previousElementSibling;
  
  if (!unitBody || !header) return;

  const isExpanded = header.classList.contains('expanded');

  if (!isExpanded) {
    header.classList.add('expanded');
    unitBody.classList.add('expanded');
    
    if (unitBody.dataset.loaded === '0') {
      unitBody.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #0b58a6;"></i></div>';
      try {
        await loadJabatan(unitId);
      } catch (e) {
        console.error(e);
      }
    }
  } else {
    header.classList.remove('expanded');
    unitBody.classList.remove('expanded');
  }
}

async function loadJabatan(unitId) {
  const unitBody = document.getElementById(`unit-body-${unitId}`);
  if (!unitBody) return;

  try {
    const url = getJabatanUrl.replace(':id', unitId);
    const res = await fetch(url, {
      headers: { 'Accept': 'application/json' },
      credentials: 'same-origin'
    });
    if (!res.ok) {
      unitBody.innerHTML = '<div class="no-data-message" style="padding: 12px;"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat</p></div>';
      unitBody.dataset.loaded = '0';
      return;
    }
    const json = await res.json();
    renderJabatan(unitBody, json.data || {});
  } catch (e) {
    console.error('Error loadJabatan:', e);
    unitBody.innerHTML = '<div class="no-data-message" style="padding: 12px;"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat</p></div>';
    unitBody.dataset.loaded = '0';
  }
}

function renderJabatan(unitBody, grouped) {
  if (!grouped || Object.keys(grouped).length === 0) {
    unitBody.innerHTML = '<div class="no-data-message" style="padding: 12px;"><i class="fas fa-inbox"></i><p>Belum ada Jabatan</p></div>';
    unitBody.dataset.loaded = '1';
    return;
  }

  let html = '<div class="jabatan-list">';
  
  // Iterate over grouped jabatan by unit code
  Object.keys(grouped).sort().forEach(unitCode => {
    const jabatans = grouped[unitCode];
    
    jabatans.forEach((jab) => {
      const gap = (jab.b || 0) - (jab.k || 0);
      const gapClass = gap > 0 ? 'badge-gap-pos' : (gap < 0 ? 'badge-gap-neg' : 'badge-gap-zero');
      html += `
        <div class="jabatan-row">
          <div class="jabatan-kode">${escapeHtml(jab.kode)}</div>
          <div class="jabatan-nama">${escapeHtml(jab.nama)}</div>
          <div class="jabatan-stats">
            <span class="stat-badge badge-b">B:${jab.b || 0}</span>
            <span class="stat-badge badge-k">K:${jab.k || 0}</span>
            <span class="stat-badge ${gapClass}">${gap > 0 ? '+' : ''}${gap}</span>
          </div>
          <div class="jabatan-actions">
            <a href="${jabatanBaseUrl}/${jab.id}/edit" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
            <button class="btn-action btn-delete" onclick="confirmDelete(${jab.id})"><i class="fas fa-trash"></i></button>
          </div>
        </div>
      `;
    });
  });
  
  html += '</div>';

  unitBody.innerHTML = html;
  unitBody.dataset.loaded = '1';
}

async function serverSearch(query) {
  if (isSearching || query.trim().length === 0) return;
  
  const normalView = document.getElementById('normalView');
  const searchResults = document.getElementById('searchResults');
  if (!normalView || !searchResults) return;
  
  isSearching = true;
  normalView.style.display = 'none';
  searchResults.classList.add('show');
  searchResults.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #0b58a6;"></i></div>';

  try {
    const url = `${searchUrl}?q=${encodeURIComponent(query)}`;
    const res = await fetch(url, { 
      headers: { 'Accept': 'application/json' }, 
      credentials: 'same-origin' 
    });
    if (!res.ok) {
      searchResults.innerHTML = `<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal melakukan pencarian</p></div>`;
      isSearching = false;
      return;
    }
    const json = await res.json();
    renderSearchResults(searchResults, json.data || []);
    isSearching = false;
  } catch (e) {
    console.error('Error serverSearch:', e);
    searchResults.innerHTML = '<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal melakukan pencarian</p></div>';
    isSearching = false;
  }
}

function renderSearchResults(container, jabatans) {
  if (!jabatans || jabatans.length === 0) {
    container.innerHTML = '<div class="no-data-message"><i class="fas fa-search"></i><p>Tidak ada hasil yang sesuai</p></div>';
    return;
  }

  // Group by PD -> Unit Name
  const grouped = {};
  jabatans.forEach(jab => {
    const pdName = jab._pd_name || 'Unknown';
    const unitName = jab._unit_name || 'Unknown';
    if (!grouped[pdName]) grouped[pdName] = {};
    if (!grouped[pdName][unitName]) grouped[pdName][unitName] = [];
    grouped[pdName][unitName].push(jab);
  });

  let html = '';
  Object.keys(grouped).forEach(pdName => {
    const units = grouped[pdName];
    html += `<div class="pd-group">
      <div class="pd-header-search">
        <div><i class="fas fa-building" style="color: #0b58a6; margin-right: 8px;"></i><strong>${escapeHtml(pdName)}</strong></div>
      </div>`;

    Object.keys(units).forEach(unitName => {
      const jabatanList = units[unitName];
      html += `<div style="padding: 8px 12px; background: #fafbfc; border-bottom: 1px solid #f0f0f0; font-weight: 500; font-size: 12px; border-left: 3px solid #e3f2fd;">
        ${escapeHtml(unitName)} (${jabatanList.length} Posisi)
      </div>`;

      html += '<div class="pd-results">';
      jabatanList.forEach((jab, idx) => {
        const gap = (jab.b || 0) - (jab.k || 0);
        const gapClass = gap > 0 ? 'badge-gap-pos' : (gap < 0 ? 'badge-gap-neg' : 'badge-gap-zero');
        const hidden = idx >= SHOW_FIRST ? 'style="display:none;" data-hidden="1"' : '';
        html += `
          <div class="jabatan-row" ${hidden}>
            <div class="jabatan-kode">${escapeHtml(jab.kode)}</div>
            <div class="jabatan-nama">${escapeHtml(jab.nama)}</div>
            <div class="jabatan-stats">
              <span class="stat-badge badge-b">B:${jab.b || 0}</span>
              <span class="stat-badge badge-k">K:${jab.k || 0}</span>
              <span class="stat-badge ${gapClass}">${gap > 0 ? '+' : ''}${gap}</span>
            </div>
            <div class="jabatan-actions">
              <a href="${jabatanBaseUrl}/${jab.id}/edit" class="btn-action btn-edit"><i class="fas fa-edit"></i></a>
              <button class="btn-action btn-delete" onclick="confirmDelete(${jab.id})"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
      });

      if (jabatanList.length > SHOW_FIRST) {
        html += `<div style="text-align: center; padding: 12px;">
          <button class="show-more-btn" onclick="expandResults(this)">
            <i class="fas fa-chevron-down"></i> Tampilkan semua (${jabatanList.length})
          </button>
        </div>`;
      }
      html += '</div>';
    });

    html += '</div>';
  });

  container.innerHTML = html;
}

function expandResults(btn) {
  const parent = btn.closest('.pd-results') || btn.closest('.pd-group');
  if (!parent) return;
  parent.querySelectorAll('[data-hidden="1"]').forEach(el => {
    el.style.display = '';
    el.removeAttribute('data-hidden');
  });
  btn.style.display = 'none';
}

function clearSearch() {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.value = '';
  
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
  
  isSearching = false;
  
  const normalView = document.getElementById('normalView');
  const searchResults = document.getElementById('searchResults');
  
  if (normalView && searchResults) {
    normalView.style.display = 'block';
    searchResults.classList.remove('show');
    searchResults.innerHTML = '';
  }
}

function confirmDelete(id) {
  if (confirm('Yakin ingin menghapus jabatan ini?')) {
    const form = document.getElementById('deleteForm');
    form.action = '{{ route("jabatan.destroy", ":id") }}'.replace(':id', id);
    form.submit();
  }
}
</script>
@endsection