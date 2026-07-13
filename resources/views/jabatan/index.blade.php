@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('css')
<style>
  /* ===== CONTAINER & LAYOUT ===== */
.search-container { 
  margin: 0;
  padding: 16px 8px 8px;
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

  .search-clear-btn:hover {
    color: #999;
  }

  .search-box input:not(:placeholder-shown) ~ .search-clear-btn { 
    display: block; 
  }

  /* ===== JABATAN HEADER ===== */
  .perangkat-header { 
    background: #f8f9fa;
    border-left: none;
    color: #2c3e50; 
    font-weight: 400;
    padding: 10px 12px;
    margin-top: 8px;
    margin-bottom: 8px;
    cursor: pointer; 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    transition: all 0.12s ease;
  }

  .perangkat-header:hover {
    background: #f0f2f5;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .perangkat-header-title { 
    display: flex; 
    align-items: center; 
    gap: 8px;
    font-size: 14px; 
    flex: 1;
  }

  .perangkat-header i:first-child {
    font-size: 14px;
    color: #0b58a6;
    flex-shrink: 0;
  }

  .perangkat-name { 
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

  .perangkat-header.open .toggle-icon { 
    transform: rotate(0deg); 
  }

  /* ===== JABATAN DETAILS ===== */
  .jabatan-details { 
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

  .jabatan-details.open { 
    display: block; 
  }

  /* ===== UNIT GROUP ===== */
  .unit-group {
    border-bottom: 1px solid #f0f0f0;
    padding: 8px 0;
  }

  .unit-group:last-child {
    border-bottom: none;
  }

  .unit-header {
    padding: 8px 12px;
    background-color: #fafbfc;
    font-size: 13px;
    color: #2c3e50;
    font-weight: 500;
  }

  /* ===== JABATAN LIST ===== */
  .jabatan-list { 
    display: flex;
    flex-direction: column;
    padding: 0 12px;
  }

  .jabatan-row { 
    display: grid; 
    grid-template-columns: 80px 1fr 100px auto;
    gap: 12px; 
    align-items: center; 
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
    font-size: 12px;
  }

  .jabatan-row:last-child {
    border-bottom: none;
  }

  .jabatan-kode { 
    background: #eaf6ff;
    color: #0b58a6; 
    padding: 5px 8px; 
    border-radius: 4px; 
    font-weight: 400; 
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
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

.jabatan-stats {
  display: flex;
  gap: 6px;
  justify-content: flex-end;
}

.stat-badge {
  padding: 4px 8px;      /* ← Dari 2px 5px menjadi 4px 8px */
  border-radius: 3px;
  font-size: 12px;
  font-weight: 500;
  min-width: 32px;       /* ← Dari 28px menjadi 32px */
  text-align: center;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 28px;
}

  .badge-b { background-color: #e3f2fd; color: #0b58a6; }
  .badge-k { background-color: #e8f5e9; color: #2e7d32; }
  .badge-gap-neg { background-color: #f7e2e5; color: #c62828; }
  .badge-gap-pos { background-color: #fff3e0; color: #e65100; }
  .badge-gap-zero { background-color: #f0f0f0; color: #616161; }

  /* ===== JABATAN ACTIONS ===== */
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
  }

  .btn-edit {
    background-color: #ffc107;
    color: #000;
  }

  .btn-edit:hover {
    background-color: #ffb300;
  }

  .btn-delete {
    background-color: #dc3545;
    color: #fff;
  }

  .btn-delete:hover {
    background-color: #c82333;
  }

  /* ===== NO DATA ===== */
  .no-data-message {
    text-align: center;
    padding: 28px 12px;
    color: #7f8c8d;
  }

  .no-data-message i {
    font-size: 28px;
    margin-bottom: 8px;
    display: block;
    color: #bdc3c7;
  }

  .no-data-message p {
    font-size: 13px;
    margin: 0;
  }

/* ===== CARD HEADER ===== */
.card-header {
  background: linear-gradient(135deg, #0b2545 0%, #0b58a6 100%);
  padding: 8px 12px;  /* ← Kurangi dari 16px */
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 8px;
}

.card-header h5 {
  color: #fff;
  font-weight: 500;
  font-size: 16px;
  margin: 0;
  line-height: 1.1;  /* ← Tambahkan untuk kompak */
  display: flex;
  align-items: center;
  gap: 8px;
}

  .card-body {
    padding: 0;
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
    padding: 16px;
  }

  .search-results-view.show {
    display: block;
  }

  @media (max-width: 768px) {
    .jabatan-row { 
      grid-template-columns: 1fr; 
      gap: 8px; 
    }
    .jabatan-actions { 
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
              <div class="perangkat-header collapsed" data-pd-id="{{ $pd->id }}" data-has-jabatan="{{ $pd->jabatan_count > 0 ? 'true' : 'false' }}">
                <div class="perangkat-header-title">
                  <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                  <i class="fas fa-building"></i>
                  <span class="perangkat-name">{{ $pd->nama }}</span>
                  <span class="unit-count-badge">{{ $pd->jabatan_count }} Jabatan</span>
                </div>
              </div>

              <div class="jabatan-details" id="details-{{ $pd->id }}" data-loaded="0"></div>
            @endforeach
          </div>

          <div id="searchResultsView" class="search-results-view main-content"></div>
        @else
          <div class="alert alert-info" style="margin: 16px;">
            <i class="fas fa-info-circle"></i> Belum ada data Perangkat Daerah
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<!-- Hidden Form for Delete -->
<form id="deleteForm" method="POST" style="display:none;">
  @csrf
  @method('DELETE')
</form>

@endsection

@section('js')
<script>
const jabatanBaseUrl = "{{ url('jabatan') }}";
const getJabatanUrl = "{{ route('jabatan.getByPerangkat', ':id') }}";
const searchUrl = "{{ route('jabatan.search') }}";
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

let searchDebounceTimer = null;
let isSearching = false;
let originalView = '';

function escapeHtml(unsafe) {
  if (unsafe === null || unsafe === undefined) return '';
  return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) {
    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' })[s];
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const normalView = document.getElementById('normalView');
  if (normalView) {
    originalView = normalView.innerHTML;
  }

  document.querySelectorAll('.perangkat-header').forEach(header => {
    header.addEventListener('click', function() {
      togglePerangkat(this);
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

  updateTotalJabatan();
});

async function togglePerangkat(headerElement) {
  if (!headerElement) return;
  
  const pdId = headerElement.getAttribute('data-pd-id');
  const hasJabatan = headerElement.getAttribute('data-has-jabatan') === 'true';
  const detailsElement = document.getElementById('details-' + pdId);
  
  if (!detailsElement) return;
  
  const isOpen = headerElement.classList.contains('open');

  if (!isOpen) {
    if (!hasJabatan) {
      detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-inbox"></i><p>Belum ada Jabatan</p></div>';
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
        await loadJabatan(pdId);
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

async function loadJabatan(pdId) {
  const detailsElement = document.getElementById('details-' + pdId);
  if (!detailsElement) return;
  
  try {
    const url = getJabatanUrl.replace(':id', pdId) + '?per_page=999&page=1';
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
    renderJabatanIntoDetails(detailsElement, json);
  } catch (e) {
    console.error('Error loadJabatan:', e);
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data</p></div>';
    detailsElement.dataset.loaded = '0';
  }
}

function renderJabatanIntoDetails(detailsElement, json) {
  const data = json.data || {};

  if (Object.keys(data).length === 0) {
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-inbox"></i><p>Belum ada Jabatan</p></div>';
    detailsElement.dataset.loaded = '1';
    return;
  }

  let html = '';
  Object.keys(data).forEach(unitName => {
    const jabatanList = data[unitName];
    html += `<div class="unit-group">
              <div class="unit-header">${escapeHtml(unitName)} (${jabatanList.length} Posisi)</div>
              <div class="jabatan-list">`;

    jabatanList.forEach((jab) => {
      const gap = (jab.b || 0) - (jab.k || 0);
      const gapClass = gap > 0 ? 'badge-gap-pos' : (gap < 0 ? 'badge-gap-neg' : 'badge-gap-zero');
      html += `
        <div class="jabatan-row">
          <div class="jabatan-kode">${escapeHtml(jab.kode)}</div>
          <div class="jabatan-nama" title="${escapeHtml(jab.nama)}">${escapeHtml(jab.nama)}</div>
          <div class="jabatan-stats">
            <span class="stat-badge badge-b">B:${jab.b || 0}</span>
            <span class="stat-badge badge-k">K:${jab.k || 0}</span>
            <span class="stat-badge ${gapClass}">${gap > 0 ? '+' : ''}${gap}</span>
          </div>
          <div class="jabatan-actions">
            <a href="${jabatanBaseUrl}/${jab.id}/edit" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
            <button class="btn-action btn-delete" onclick="confirmDelete(${jab.id})" title="Hapus"><i class="fas fa-trash"></i></button>
          </div>
        </div>
      `;
    });

    html += `</div></div>`;
  });

  detailsElement.innerHTML = html;
  detailsElement.dataset.loaded = '1';
}

async function serverSearch(query) {
  if (isSearching || query.trim().length === 0) return;
  
  const normalView = document.getElementById('normalView');
  const searchResultsView = document.getElementById('searchResultsView');
  if (!normalView || !searchResultsView) return;
  
  isSearching = true;
  normalView.style.display = 'none';
  searchResultsView.classList.add('show');
  searchResultsView.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #0b58a6;"></i></div>';

  try {
    const url = `${searchUrl}?q=${encodeURIComponent(query)}`;
    const res = await fetch(url, { 
      headers: { 'Accept': 'application/json' }, 
      credentials: 'same-origin' 
    });
    if (!res.ok) {
      searchResultsView.innerHTML = `<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal melakukan pencarian</p></div>`;
      isSearching = false;
      return;
    }
    const json = await res.json();
    renderSearchResults(searchResultsView, json.data || {});
    isSearching = false;
  } catch (e) {
    console.error('Error serverSearch:', e);
    searchResultsView.innerHTML = '<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal melakukan pencarian</p></div>';
    isSearching = false;
  }
}

function renderSearchResults(container, data) {
  if (Object.keys(data).length === 0) {
    container.innerHTML = '<div class="no-data-message"><i class="fas fa-search"></i><p>Tidak ada hasil yang sesuai</p></div>';
    return;
  }

  let html = '';
  Object.keys(data).forEach(pdName => {
    const units = data[pdName];
    html += `<div style="margin-bottom: 12px;">
              <div style="padding: 10px 12px; background: #f8f9fa; border: 1px solid #e8eaed; border-radius: 6px 6px 0 0;">
                <i class="fas fa-building" style="color: #0b58a6; margin-right: 8px;"></i><strong>${escapeHtml(pdName)}</strong>
              </div>`;

    Object.keys(units).forEach(unitName => {
      const jabatanList = units[unitName];
      html += `<div style="padding: 8px 12px; background: #fafbfc; border-bottom: 1px solid #f0f0f0; font-weight: 500; font-size: 12px;">
                ${escapeHtml(unitName)} (${jabatanList.length} Posisi)
              </div>`;

      jabatanList.forEach((jab) => {
        const gap = (jab.b || 0) - (jab.k || 0);
        const gapClass = gap > 0 ? 'badge-gap-pos' : (gap < 0 ? 'badge-gap-neg' : 'badge-gap-zero');
        html += `
          <div class="jabatan-row" style="padding: 8px 12px; background: #fff; border-bottom: 1px solid #f0f0f0;">
            <div class="jabatan-kode">${escapeHtml(jab.kode)}</div>
            <div class="jabatan-nama" title="${escapeHtml(jab.nama)}">${escapeHtml(jab.nama)}</div>
            <div class="jabatan-stats">
              <span class="stat-badge badge-b">B:${jab.b || 0}</span>
              <span class="stat-badge badge-k">K:${jab.k || 0}</span>
              <span class="stat-badge ${gapClass}">${gap > 0 ? '+' : ''}${gap}</span>
            </div>
            <div class="jabatan-actions">
              <a href="${jabatanBaseUrl}/${jab.id}/edit" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
              <button class="btn-action btn-delete" onclick="confirmDelete(${jab.id})" title="Hapus"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
      });
    });

    html += `</div>`;
  });

  container.innerHTML = html;
}

function clearSearch() {
  const searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.value = '';
  
  if (searchDebounceTimer) clearTimeout(searchDebounceTimer);
  
  isSearching = false;
  
  const normalView = document.getElementById('normalView');
  const searchResultsView = document.getElementById('searchResultsView');
  
  if (normalView && searchResultsView) {
    normalView.style.display = 'block';
    searchResultsView.classList.remove('show');
    searchResultsView.innerHTML = '';
  }
}

async function confirmDelete(id) {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    const form = document.getElementById('deleteForm');
    form.action = '{{ route("jabatan.destroy", ":id") }}'.replace(':id', id);
    form.submit();
  }
}

function updateTotalJabatan() {
  // Update total jabatan count dari semua perangkat
  let total = 0;
  document.querySelectorAll('[data-pd-id]').forEach(header => {
    const badges = header.querySelector('.unit-count-badge');
    if (badges) {
      const count = parseInt(badges.textContent) || 0;
      total += count;
    }
  });
  document.getElementById('totalJabatan').textContent = total;
}
</script>
@endsection