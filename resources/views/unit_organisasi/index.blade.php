{{-- resources/views/unit_organisasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Unit Organisasi')

@section('css')
<style>
  /* ===== CONTAINER & LAYOUT ===== */
  .search-container { 
    margin-bottom: 10px; /* reduce vertical gap */
    display: flex; 
    gap: 10px; 
    align-items: center; 
    justify-content: flex-end; 
    flex-wrap: wrap; 
  }

  .search-box { 
    flex: 0 1 200px; /* make search shorter */
    max-width: 260px;
    position: relative; 
  }

  .search-box input { 
    width: 100%; 
    padding: 8px 30px 8px 12px; /* reduce padding */
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

  /* ===== OPD HEADER (Perangkat Daerah) - COMPACT ===== */
  .opd-header { 
    background: #f8f9fa;
    border-left: none;
    color: #2c3e50; 
    font-weight: 400;
    padding: 10px 12px; /* smaller padding */
    margin-top: 8px; /* smaller gap between items */
    cursor: pointer; 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    transition: all 0.12s ease;
  }

  .opd-header:hover {
    background: #f0f2f5;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
  }

  .opd-header-title { 
    display: flex; 
    align-items: center; 
    gap: 8px; /* reduce gap */
    font-size: 14px; 
    flex: 1;
  }

  .opd-header i:first-child {
    font-size: 14px;
    color: #0b58a6;
    flex-shrink: 0;
  }

  .opd-name { 
    font-weight: 400; 
    color: #2c3e50; 
    font-size: 13px; /* slightly smaller to fit */
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: calc(100% - 120px); /* keep space for badge */
  }

  .unit-count-badge { 
    background: #e3f2fd;
    color: #0b58a6; 
    padding: 4px 8px; /* smaller badge */
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

  .opd-header.open .toggle-icon { 
    transform: rotate(0deg); 
  }

  /* ===== UNIT DETAILS CONTAINER ===== */
  .unit-details { 
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

  .unit-details.open { 
    display: block; 
  }

  /* ===== UNIT LIST (COMPACT) ===== */
  .unit-list {
    display: flex;
    flex-direction: column;
  }

  .unit-row { 
    display: grid; 
    grid-template-columns: 90px 1fr 130px auto; /* compact columns */
    gap: 12px; 
    align-items: center; 
    padding: 8px 12px; /* reduced padding */
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
  }

  .unit-row:last-child {
    border-bottom: none;
  }

  /* ===== LEVEL STYLING (Indentation) ===== */
  .unit-row[data-level="1"] { 
    padding-left: 12px; 
    background-color: #fafbfc;
    font-weight: 400;
  }

  .unit-row[data-level="2"] { 
    padding-left: 28px; 
    background-color: #f8f9fb; 
  }

  .unit-row[data-level="3"] { 
    padding-left: 44px; 
    background-color: #ffffff; 
  }

  /* ===== UNIT CODE (KODE) ===== */
  .unit-kode { 
    background: #eaf6ff; /* lighter for compact */
    color: #0b58a6; 
    padding: 5px 8px; 
    border-radius: 4px; 
    font-weight: 400; 
    font-size: 12px; 
    text-align: center;
    min-width: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 28px;
  }

  /* ===== UNIT INFO ===== */
  .unit-info { 
    display: flex; 
    flex-direction: column; 
    gap: 2px;
  }

  .unit-nama { 
    font-weight: 400; 
    color: #2c3e50; 
    font-size: 13px;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* ===== UNIT ACTIONS ===== */
  .unit-actions { 
    display: flex; 
    gap: 8px; 
    justify-content: flex-end;
  }

  .btn-action {
    border: none;
    cursor: pointer;
    padding: 6px 10px;
    font-size: 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
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

  /* ===== SEARCH RESULTS ===== */
  .pd-group { 
    margin-bottom: 12px; 
  }

  .pd-group .pd-header { 
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

  .pd-group .pd-header strong {
    color: #2c3e50;
    font-weight: 400;
    font-size: 13px;
  }

  .pd-results { 
    margin-top: 0; 
    background: #fff; 
    border: 1px solid #e8eaed; 
    border-radius: 0 0 6px 6px; 
    overflow: auto; 
  }

  .pd-results .unit-row { 
    grid-template-columns: 80px 1fr 120px auto; 
  }

  .show-more-btn { 
    display: inline-block; 
    margin-top: 8px; 
    background: #eef6ff;
    color: #0b58a6; 
    border: 1px solid #cfe4ff;
    padding: 8px 12px; 
    border-radius: 6px; 
    cursor: pointer; 
    font-size: 13px;
    font-weight: 400;
    transition: all 0.12s ease;
  }

  .show-more-btn:hover {
    background: #dce8ff;
    border-color: #b3d4ff;
  }

  /* ===== NO DATA MESSAGE ===== */
  .no-data-message {
    text-align: center;
    padding: 28px 12px; /* smaller */
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
  }

  .card-header h5 {
    color: #fff;
    font-weight: 500;
    font-size: 16px;
  }

  .card-header i {
    margin-right: 8px;
  }

  /* ===== RESPONSIVE ===== */
  @media (max-width: 768px) { 
    .search-container { 
      justify-content: flex-start; 
      flex-direction: column; 
    }

    .opd-header-title {
      flex-direction: column;
      align-items: flex-start;
    }

    .unit-count-badge {
      margin-left: 0;
      margin-top: 4px;
    }

    .unit-row { 
      grid-template-columns: 1fr; 
      gap: 8px; 
    } 

    .unit-row[data-level="1"] { 
      padding-left: 12px; 
    }

    .unit-row[data-level="2"] { 
      padding-left: 28px; 
    }

    .unit-row[data-level="3"] { 
      padding-left: 44px; 
    }

    .unit-actions { 
      justify-content: flex-start;
      flex-wrap: wrap;
    }

    .pd-results .unit-row { 
      grid-template-columns: 1fr; 
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
        <a href="{{ route('unit_organisasi.create') }}" class="btn btn-sm btn-light">
          <i class="fas fa-plus"></i> Tambah UNOR
        </a>
      </div>

      <div class="card-body">
        @if($perangkatDaerah->count() > 0)
          <div class="search-container">
            <div class="search-box">
              <input type="text" id="searchInput" placeholder="Cari unit organisasi..." autocomplete="off" />
              <button class="search-clear-btn" onclick="clearSearch()" title="Hapus pencarian">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div id="opdContainer">
            @foreach($perangkatDaerah as $opd)
              <div class="opd-header collapsed" data-opd-id="{{ $opd->id }}" data-has-units="{{ $opd->unit_organisasi_count > 0 ? 'true' : 'false' }}">
                <div class="opd-header-title">
                  <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                  <i class="fas fa-building"></i>
                  <span class="opd-name">{{ $opd->nama }}</span>
                  <span class="unit-count-badge">{{ $opd->unit_organisasi_count }} Unit</span>
                </div>
              </div>

              <div class="unit-details" id="details-{{ $opd->id }}" data-loaded="0"></div>
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
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const unitsBaseUrl = "{{ url('perangkat_daerah') }}";
const searchUrl = "{{ route('unit_organisasi.search') }}";
const unitBaseUrl = "{{ url('unit_organisasi') }}";

let searchDebounceTimer = null;
const SHOW_FIRST = 8;

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.opd-header').forEach(header => {
    header.addEventListener('click', () => toggleOPD(header));
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
        serverSearch(q);
      }, 300);
    });
  }
});

async function toggleOPD(headerElement) {
  if (!headerElement) return;
  const opdId = headerElement.getAttribute('data-opd-id');
  const hasUnits = headerElement.getAttribute('data-has-units') === 'true';
  const detailsElement = document.getElementById('details-' + opdId);
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
        await loadUnits(opdId);
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

async function loadUnits(opdId) {
  const detailsElement = document.getElementById('details-' + opdId);
  if (!detailsElement) return;
  
  try {
    const url = `${unitsBaseUrl}/${opdId}/units?per_page=999&page=1`;
    const res = await fetch(url, { 
      headers: { 'Accept': 'application/json' }, 
      credentials: 'same-origin' 
    });
    if (!res.ok) {
      const txt = await res.text();
      console.error('loadUnits failed', res.status, txt);
      detailsElement.innerHTML = `<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data (HTTP ${res.status})</p></div>`;
      detailsElement.dataset.loaded = '0';
      return;
    }
    const json = await res.json();
    renderUnitsIntoDetails(detailsElement, json);
  } catch (e) {
    console.error('Error loadUnits:', e);
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data</p></div>';
    detailsElement.dataset.loaded = '0';
  }
}

function renderUnitsIntoDetails(detailsElement, json) {
  const units = json.data || [];

  if (units.length === 0) {
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-inbox"></i><p>Belum ada Unit Organisasi</p></div>';
    detailsElement.dataset.loaded = '1';
    return;
  }

  let html = '<div class="unit-list">';
  units.forEach((u, idx) => {
    const level = u._level || 1;
    html += `
      <div class="unit-row" data-level="${level}">
        <div class="unit-kode">${escapeHtml(u.kode)}</div>
        <div class="unit-info">
          <div class="unit-nama">${escapeHtml(u.nama)}</div>
        </div>
        <div class="unit-actions">
          <a href="${unitBaseUrl}/${u.id}/edit" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>
          <button class="btn-action btn-delete" onclick="deleteUnit(${u.id}, ${u.perangkat_daerah_id})" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
        </div>
      </div>
    `;
  });
  html += '</div>';

  detailsElement.innerHTML = html;
  detailsElement.dataset.loaded = '1';
}

async function serverSearch(query) {
  const container = document.getElementById('opdContainer');
  if (!container) return;
  container.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #0b58a6;"></i> <p style="margin-top: 12px; color: #666;">Mencari...</p></div>';

  try {
    const perPage = 500;
    const url = `${searchUrl}?q=${encodeURIComponent(query)}&per_page=${perPage}`;
    const res = await fetch(url, { 
      headers: { 'Accept': 'application/json' }, 
      credentials: 'same-origin' 
    });
    if (!res.ok) {
      const txt = await res.text();
      console.error('Search failed', res.status, txt);
      container.innerHTML = `<div class="no-results"><i class="fas fa-exclamation-circle"></i><p>Gagal melakukan pencarian</p></div>`;
      return;
    }
    const json = await res.json();
    renderSearchResults(container, json);
  } catch (e) {
    console.error('Error serverSearch:', e);
    container.innerHTML = '<div class="no-results"><i class="fas fa-exclamation-circle"></i><p>Gagal melakukan pencarian</p></div>';
  }
}

function renderSearchResults(container, json) {
  const units = json.data || [];
  if (units.length === 0) {
    container.innerHTML = '<div class="no-results"><i class="fas fa-search"></i><p>Tidak ada hasil yang sesuai</p></div>';
    return;
  }

  const grouped = {};
  units.forEach(u => {
    const pdName = (u.perangkat_daerah && u.perangkat_daerah.nama) ? u.perangkat_daerah.nama : ('PD #' + u.perangkat_daerah_id);
    if (!grouped[pdName]) grouped[pdName] = [];
    grouped[pdName].push(u);
  });

  let html = '';
  Object.keys(grouped).forEach(pdName => {
    const arr = grouped[pdName];
    html += `<div class="pd-group">
              <div class="pd-header">
                <div><i class="fas fa-building" style="color: #0b58a6; margin-right: 8px;"></i><strong>${escapeHtml(pdName)}</strong></div>
                <div><span class="unit-count-badge">${arr.length} Unit</span></div>
              </div>`;

    html += `<div class="pd-results">`;
    arr.forEach((u, idx) => {
      const level = u._level || 1;
      const hiddenClass = idx >= SHOW_FIRST ? 'style="display:none;" data-hidden="1"' : '';
      html += `
        <div class="unit-row" data-level="${level}" ${hiddenClass}>
          <div class="unit-kode">${escapeHtml(u.kode)}</div>
          <div class="unit-info">
            <div class="unit-nama">${escapeHtml(u.nama)}</div>
          </div>
          <div class="unit-actions">
            <a href="${unitBaseUrl}/${u.id}/edit" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>
            <button class="btn-action btn-delete" onclick="deleteUnit(${u.id}, ${u.perangkat_daerah_id})" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
          </div>
        </div>
      `;
    });

    if (arr.length > SHOW_FIRST) {
      html += `<div style="text-align: center; padding: 12px;"><button class="show-more-btn" onclick="expandPdResults(this, ${SHOW_FIRST})"><i class="fas fa-chevron-down"></i> Tampilkan semua (${arr.length})</button></div>`;
    }

    html += `</div></div>`;
  });

  container.innerHTML = html;
}

function expandPdResults(btn, limit) {
  const pdResults = btn.closest('.pd-results');
  if (!pdResults) return;
  pdResults.querySelectorAll('[data-hidden="1"]').forEach(el => {
    el.style.display = '';
    el.removeAttribute('data-hidden');
  });
  btn.style.display = 'none';
}

async function deleteUnit(id, opdId) {
  if (!confirm('Yakin ingin menghapus unit organisasi ini?')) return;
  try {
    const res = await fetch(`${unitBaseUrl}/${id}`, {
      method: 'DELETE',
      credentials: 'same-origin',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    if (!res.ok) {
      const txt = await res.text();
      console.error('Delete failed', res.status, txt);
      alert('Gagal menghapus unit organisasi.');
      return;
    }
    const json = await res.json();
    if (json.success) {
      const detailsElement = document.getElementById('details-' + opdId);
      if (detailsElement && detailsElement.dataset.loaded === '1' && detailsElement.style.display !== 'none') {
        loadUnits(opdId);
      }
    } else {
      alert(json.message || 'Gagal menghapus unit organisasi.');
    }
  } catch (e) {
    console.error('Error deleteUnit:', e);
    alert('Terjadi kesalahan saat menghapus.');
  }
}

function clearSearch() {
  const searchInput = document.getElementById('searchInput');
  if (!searchInput) return;
  searchInput.value = '';
  searchInput.focus();
  document.querySelectorAll('.opd-header').forEach(h => {
    h.style.display = '';
    h.classList.remove('open');
  });
  document.querySelectorAll('.unit-details').forEach(d => {
    d.classList.remove('open');
    d.style.display = 'none';
  });
}

function escapeHtml(unsafe) {
  if (unsafe === null || unsafe === undefined) return '';
  return String(unsafe).replace(/[&<>"'`=\/]/g, function (s) {
    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;', '/': '&#x2F;', '`': '&#x60;', '=': '&#x3D;' })[s];
  });
}
</script>
@endsection