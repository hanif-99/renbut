{{-- resources/views/unit_organisasi/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Unit Organisasi')

@section('css')
<style>
  .search-container { 
    margin-bottom: 20px; 
    display: flex; 
    gap: 10px; 
    align-items: center; 
    justify-content: flex-end; 
    flex-wrap: wrap; 
  }

  .search-box { 
    flex: 0 1 360px; 
    position: relative; 
  }

  .search-box input { 
    width: 100%; 
    padding: 10px 35px 10px 14px; 
    border: 1px solid #ddd; 
    border-radius: 6px; 
    font-size: 14px; 
  }

  .search-box input:focus { 
    outline: none; 
    border-color: #0b58a6; 
    box-shadow: 0 0 0 3px rgba(11, 88, 166, 0.06); 
  }

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
  }

  .search-box input:not(:placeholder-shown) ~ .search-clear-btn { 
    display: block; 
  }

  .opd-header { 
    background-color: #f8f9fa; 
    border-left: 4px solid #ccc; 
    color: #333; 
    font-weight: 600; 
    padding: 14px 16px; 
    margin-top: 12px; 
    cursor: pointer; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
  }

  .opd-header-title { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    font-size: 14px; 
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
    font-size: 12px; 
    transform: rotate(-90deg); 
  }

  .opd-header.open .toggle-icon { 
    transform: rotate(0deg); 
  }

  .unit-details { 
    display: none; 
    padding: 12px 16px 16px 16px; 
    background-color: #fafafa; 
    margin-bottom: 10px; 
    border-radius: 0 0 6px 6px; 
    border: 1px solid #e0e0e0; 
    border-top: none; 
  }

  .unit-row { 
    display: grid; 
    grid-template-columns: 40px 85px 1fr 140px auto; 
    gap: 12px; 
    align-items: center; 
    padding: 10px 0; 
    border-bottom: 1px solid #e8e8e8; 
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
    color: #999;
  }

  .unit-actions { 
    display: flex; 
    gap: 6px; 
    justify-content: flex-end; 
  }

  .btn-action {
    border: none;
    cursor: pointer;
    padding: 6px 10px;
    font-size: 13px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 4px;
    text-decoration: none;
  }

  .btn-edit {
    background-color: #ffc107;
    color: #000;
  }

  .btn-delete {
    background-color: #dc3545;
    color: #fff;
  }

  .search-results .unit-list { 
    display: block; 
  }

  .pd-group { 
    margin-bottom: 12px; 
  }

  .pd-group .pd-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    gap: 10px; 
    padding: 8px 12px; 
    background: #fff; 
    border: 1px solid #e8e8e8; 
    border-radius: 6px; 
  }

  .pd-results { 
    margin-top: 8px; 
    background: #fff; 
    border: 1px solid #eaeaea; 
    border-radius: 6px; 
    padding: 8px; 
    max-height: 420px; 
    overflow: auto; 
  }

  .pd-results .unit-row { 
    grid-template-columns: 40px 85px 1fr 110px auto; 
  }

  .show-more-btn { 
    display: inline-block; 
    margin-top: 8px; 
    background: #eef6ff; 
    color: #0b58a6; 
    border: 1px solid #d6ecff; 
    padding: 6px 10px; 
    border-radius: 6px; 
    cursor: pointer; 
    font-size: 13px; 
  }

  @media (max-width: 768px) { 
    .search-container { 
      justify-content: flex-start; 
      flex-direction: column; 
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
          <div class="search-container">
            <div class="search-box">
              <input type="text" id="searchInput" placeholder="Search . . ." autocomplete="off" />
              <button class="search-clear-btn" onclick="clearSearch()" title="Clear">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div id="opdContainer">
            @foreach($perangkatDaerah as $opd)
              <div class="opd-header collapsed" data-opd-id="{{ $opd->id }}" data-search-text="{{ strtolower($opd->nama) }}">
                <div class="opd-header-title">
                  <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                  <i class="fas fa-building"></i>
                  <span class="opd-name">{{ $opd->nama }}</span>
                  <span class="unit-count-badge">{{ $opd->unit_organisasi_count }} Unit</span>
                </div>
              </div>

              <div class="unit-details" id="details-{{ $opd->id }}" data-loaded="0" data-current-page="0"></div>
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
  const detailsElement = document.getElementById('details-' + opdId);
  if (!detailsElement) return;

  const isOpen = headerElement.classList.contains('open');

  if (!isOpen) {
    if (detailsElement.dataset.loaded === '0') {
      detailsElement.innerHTML = '<p>Loading…</p>';
      detailsElement.style.display = 'block';
      try {
        await loadUnits(opdId, 1);
      } catch (e) {
        console.error(e);
      }
    }
    headerElement.classList.add('open');
    detailsElement.style.display = 'block';
  } else {
    headerElement.classList.remove('open');
    detailsElement.style.display = 'none';
  }
}

async function loadUnits(opdId, page = 1) {
  const detailsElement = document.getElementById('details-' + opdId);
  if (!detailsElement) return;
  detailsElement.innerHTML = '<p>Loading…</p>';
  try {
    const perPage = 50;
    const url = `${unitsBaseUrl}/${opdId}/units?per_page=${perPage}&page=${page}`;
    const res = await fetch(url, { 
      headers: { 'Accept': 'application/json' }, 
      credentials: 'same-origin' 
    });
    if (!res.ok) {
      const txt = await res.text();
      console.error('loadUnits failed', res.status, txt);
      detailsElement.innerHTML = `<p class="text-danger">Gagal memuat data (HTTP ${res.status}).</p>`;
      detailsElement.dataset.loaded = '0';
      return;
    }
    const json = await res.json();
    renderUnitsIntoDetails(detailsElement, json, opdId);
  } catch (e) {
    console.error('Error loadUnits:', e);
    detailsElement.innerHTML = '<p class="text-danger">Gagal memuat data (cek console).</p>';
    detailsElement.dataset.loaded = '0';
  }
}

function renderUnitsIntoDetails(detailsElement, json, opdId) {
  const units = json.data || [];
  const perPage = json.per_page || 50;
  const currentPage = json.current_page || 1;
  const lastPage = json.last_page || 1;

  if (units.length === 0) {
    detailsElement.innerHTML = '<div class="no-data-message"><i class="fas fa-inbox"></i><p>Belum ada Unit Organisasi</p></div>';
    detailsElement.dataset.loaded = '1';
    detailsElement.dataset.currentPage = '0';
    detailsElement.style.display = 'block';
    return;
  }

  let html = '<div class="unit-list">';
  units.forEach((u, idx) => {
    const no = ((currentPage - 1) * perPage) + (idx + 1);
    html += `
      <div class="unit-row" data-search-text="${(u.nama + ' ' + u.kode).toLowerCase()}" data-opd-id="${u.perangkat_daerah_id}">
        <div class="unit-no">${no}</div>
        <div class="unit-kode">${escapeHtml(u.kode)}</div>
        <div class="unit-info">
          <div class="unit-nama">${escapeHtml(u.nama)}</div>
          <div class="unit-atasan">${u.unor_atasan ? 'Atasan: ' + escapeHtml(u.unor_atasan) : '<span class="unit-atasan-empty">-</span>'}</div>
        </div>
        <div class="unit-actions">
          <a href="${unitBaseUrl}/${u.id}/edit" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>
          <button class="btn-action btn-delete" onclick="deleteUnit(${u.id}, ${u.perangkat_daerah_id})" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
        </div>
      </div>
    `;
  });
  html += '</div>';

  html += `<div class="mt-3 d-flex justify-content-center align-items-center gap-2">`;
  if (currentPage > 1) {
    html += `<button class="btn btn-outline-secondary btn-sm" onclick="loadUnits(${opdId}, ${currentPage - 1})">Prev</button>`;
  }
  html += `<span>Halaman ${currentPage} / ${lastPage}</span>`;
  if (currentPage < lastPage) {
    html += `<button class="btn btn-outline-secondary btn-sm" onclick="loadUnits(${opdId}, ${currentPage + 1})">Next</button>`;
  }
  html += `</div>`;

  detailsElement.innerHTML = html;
  detailsElement.dataset.loaded = '1';
  detailsElement.dataset.currentPage = currentPage.toString();
  detailsElement.style.display = 'block';
}

async function serverSearch(query) {
  const container = document.getElementById('opdContainer');
  if (!container) return;
  container.innerHTML = '<p>Searching…</p>';

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
      container.innerHTML = `<p class="text-danger">Gagal melakukan pencarian (HTTP ${res.status}).</p>`;
      return;
    }
    const json = await res.json();
    renderSearchResults(container, json);
  } catch (e) {
    console.error('Error serverSearch:', e);
    container.innerHTML = '<p class="text-danger">Gagal melakukan pencarian (cek console).</p>';
  }
}

function renderSearchResults(container, json) {
  const units = json.data || [];
  if (units.length === 0) {
    container.innerHTML = '<div id="noResults" class="no-results"><i class="fas fa-search"></i><p>Tidak ada hasil yang cocok</p></div>';
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
                <div><i class="fas fa-building"></i> <strong>${escapeHtml(pdName)}</strong></div>
                <div><span class="unit-count-badge">${arr.length} Unit</span></div>
              </div>`;

    html += `<div class="pd-results" data-pd="${escapeHtml(pdName)}">`;
    arr.forEach((u, idx) => {
      const hiddenClass = idx >= SHOW_FIRST ? 'style="display:none;" data-hidden="1"' : '';
      html += `
        <div class="unit-row" ${hiddenClass}>
          <div class="unit-no">${idx + 1}</div>
          <div class="unit-kode">${escapeHtml(u.kode)}</div>
          <div class="unit-info">
            <div class="unit-nama">${escapeHtml(u.nama)}</div>
            <div class="unit-atasan">${u.unor_atasan ? 'Atasan: ' + escapeHtml(u.unor_atasan) : '<span class="unit-atasan-empty">-</span>'}</div>
          </div>
          <div class="unit-actions">
            <a href="${unitBaseUrl}/${u.id}/edit" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>
            <button class="btn-action btn-delete" onclick="deleteUnit(${u.id}, ${u.perangkat_daerah_id})" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
          </div>
        </div>
      `;
    });

    if (arr.length > SHOW_FIRST) {
      html += `<div style="text-align:center;"><button class="show-more-btn" onclick="expandPdResults(this, ${SHOW_FIRST})">Tampilkan semua (${arr.length})</button></div>`;
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
  if (!confirm('Yakin hapus unit ini?')) return;
  try {
    const res = await fetch(`${unitBaseUrl}/${id}`, {
      method: 'DELETE',
      credentials: 'same-origin',
      headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
    });
    if (!res.ok) {
      const txt = await res.text();
      console.error('Delete failed', res.status, txt);
      alert('Gagal menghapus unit (HTTP ' + res.status + ').');
      return;
    }
    const json = await res.json();
    if (json.success) {
      const detailsElement = document.getElementById('details-' + opdId);
      if (detailsElement && detailsElement.dataset.loaded === '1' && detailsElement.style.display !== 'none') {
        const currentPage = parseInt(detailsElement.dataset.currentPage || '1', 10);
        loadUnits(opdId, currentPage);
      }
    } else {
      alert(json.message || 'Gagal menghapus unit.');
    }
  } catch (e) {
    console.error('Error deleteUnit:', e);
    alert('Terjadi kesalahan saat menghapus (cek console).');
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