{{-- resources/views/perangkat_daerah/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Perangkat Daerah')

@section('css')
<style>
  /* ===== CONTAINER & LAYOUT ===== */
  .search-container { 
    margin-bottom: 24px; 
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
    right: 10px; 
    top: 50%; 
    transform: translateY(-50%); 
    background: none; 
    border: none; 
    color: #bbb; 
    cursor: pointer; 
    font-size: 16px; 
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

  /* ===== OPD HEADER (Perangkat Daerah) - MATCH Unit Organisasi EXACTLY ===== */
  .opd-header { 
    background: #f8f9fa;
    border-left: none;
    color: #2c3e50; 
    font-weight: 400;
    padding: 14px 16px;     /* same as unit_organisasi */
    margin-top: 12px;       /* same spacing between items */
    cursor: pointer; 
    display: flex; 
    justify-content: space-between; 
    align-items: center;
    border-radius: 6px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
  }

  .opd-header:hover {
    background: #f0f2f5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
  }

  .opd-header-title { 
    display: flex; 
    align-items: center; 
    gap: 12px;           /* identical */
    font-size: 14px; 
    flex: 1;
  }

  .opd-header i:first-child {
    font-size: 16px;
    color: #0b58a6;
    flex-shrink: 0;
  }

  .opd-name { 
    font-weight: 400; 
    color: #2c3e50; 
    font-size: 13px;    /* match unit_organisasi slightly smaller for same visual */
    line-height: 1.4;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* remove any unit-count-badge (do not display counts) */

  .toggle-icon { 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    width: 20px; 
    height: 20px; 
    color: #0b58a6; 
    font-size: 10px; 
    transform: rotate(-90deg);
    flex-shrink: 0;
    transition: transform 0.2s ease;
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
    margin-bottom: 8px; 
    border-radius: 0 0 6px 6px; 
    border: 1px solid #e8eaed;
    border-top: none;
    overflow: hidden;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
  }

  .unit-details.open { 
    display: block; 
  }

  /* ===== UNIT ROW (list entries) - match spacing/padding exactly ===== */
  .unit-list {
    display: flex;
    flex-direction: column;
  }

  .unit-row { 
    display: grid; 
    /* unit_organisasi used: 45px 90px 1fr 150px auto  -> we removed the 'no' column.
       To keep visual spacing identical, we use 90px 1fr 150px auto but keep padding identical */
    grid-template-columns: 90px 1fr 150px auto; 
    gap: 14px;                 /* same as unit_organisasi */
    align-items: center; 
    padding: 12px 16px;        /* same as unit_organisasi */
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;           /* same */
  }

  .unit-row:last-child {
    border-bottom: none;
  }

  .unit-row[data-level="1"] { 
    padding-left: 16px; 
    background-color: #fafbfc;
    font-weight: 400;
  }

  .unit-row[data-level="2"] { 
    padding-left: 36px; 
    background-color: #f8f9fb; 
  }

  .unit-row[data-level="3"] { 
    padding-left: 56px; 
    background-color: #ffffff; 
  }

  /* ===== SMALL CODE BADGE (left column) - match Unit Organisasi styles exactly ===== */
  .unit-kode { 
    background: #e3f2fd;       /* same light blue */
    color: #0b58a6; 
    padding: 6px 10px;         /* same */
    border-radius: 4px; 
    font-weight: 400; 
    font-size: 12px; 
    text-align: center;
    min-width: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 28px;              /* match */
  }

  /* ===== UNIT INFO ===== */
  .unit-info { 
    display: flex; 
    flex-direction: column; 
    gap: 3px;
  }

  .unit-nama { 
    font-weight: 400; 
    color: #2c3e50; 
    font-size: 13px;
    line-height: 1.4;
  }

  /* ===== UNIT ACTIONS (button sizes same as Unit Organisasi) ===== */
  .unit-actions { 
    display: flex; 
    gap: 8px; 
    justify-content: flex-end;
  }

  .btn-action {
    border: none;
    cursor: pointer;
    padding: 6px 12px;    /* same */
    font-size: 12px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
  }

  .btn-edit {
    background-color: #ffc107;
    color: #000;
  }

  .btn-edit:hover {
    background-color: #ffb300;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
  }

  .btn-delete {
    background-color: #dc3545;
    color: #fff;
  }

  .btn-delete:hover {
    background-color: #c82333;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
  }

  /* ===== CARD HEADER - EXACT same gradient as Unit Organisasi ===== */
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

  /* ===== RESPONSIVE (same breakpoints/adjustments) ===== */
  @media (max-width: 768px) { 
    .search-container { 
      justify-content: flex-start; 
      flex-direction: column; 
    }

    .opd-header-title {
      flex-direction: column;
      align-items: flex-start;
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
        <h5 class="mb-0"><i class="fas fa-building"></i> Master Perangkat Daerah</h5>
        {{-- tombol tambah dihapus sesuai permintaan --}}
      </div>

      <div class="card-body">
        @if($perangkat->count() > 0)
          <div class="search-container">
            <div class="search-box">
              <input type="text" id="searchInput" placeholder="Cari perangkat daerah..." autocomplete="off" />
              <button class="search-clear-btn" onclick="clearSearch()" title="Hapus pencarian">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>

          <div id="opdContainer">
            @foreach($perangkat as $opd)
              {{-- We render each PD as an 'opd-header' so its look matches Unit Organisasi entries --}}
              <div class="opd-header" data-pd-id="{{ $opd->id }}" data-pd-name="{{ strtolower($opd->nama) }}">
                <div class="opd-header-title">
                  <i class="fas fa-building"></i>
                  <span class="opd-name">{{ $opd->nama }}</span>
                </div>

                <div class="unit-actions">
                  {{-- For visual parity we put Edit/Hapus as buttons on the right (same sizes as unit_organisasi) --}}
                  <a href="{{ route('perangkat_daerah.edit', $opd->id) }}" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>

                  <form action="{{ route('perangkat_daerah.destroy', $opd->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus perangkat daerah ini?')"><i class="fas fa-trash"></i> Hapus</button>
                  </form>
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

  document.addEventListener('DOMContentLoaded', () => {
    // Toggle behavior (same as Unit Organisasi): clicking header toggles details
    document.querySelectorAll('.opd-header').forEach(header => {
      header.addEventListener('click', () => {
        header.classList.toggle('open');
        const id = header.getAttribute('data-pd-id');
        const details = document.getElementById('details-' + id);
        if (!details) return;
        if (details.style.display === 'block') {
          details.style.display = 'none';
        } else {
          details.style.display = 'block';
          // details content left empty (no AJAX) — same visual behavior as Unit Organisasi collapsed/expand
        }
      });
    });

    // client-side search to filter list (fast and same UX)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('input', function(e) {
        const q = e.target.value.trim().toLowerCase();
        document.querySelectorAll('#opdContainer .opd-header').forEach(h => {
          const name = h.getAttribute('data-pd-name') || '';
          if (q === '' || name.includes(q)) {
            h.style.display = '';
            const details = h.nextElementSibling;
            if (details && details.classList.contains('unit-details')) details.style.display = '';
          } else {
            h.style.display = 'none';
            const details = h.nextElementSibling;
            if (details && details.classList.contains('unit-details')) details.style.display = 'none';
          }
        });
      });
    }
  });

  function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    searchInput.value = '';
    searchInput.focus();
    document.querySelectorAll('#opdContainer .opd-header').forEach(h => {
      h.style.display = '';
      const details = h.nextElementSibling;
      if (details && details.classList.contains('unit-details')) details.style.display = 'none';
    });
  }
</script>
@endsection