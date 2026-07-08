@extends('layouts.app')

@section('title', 'Master Perangkat Daerah')

@section('css')
<style>
  /* Tweakable variables */
  :root {
    --header-padding-vertical: 5px;   /* height/vertical padding of the blue banner */
    --row-padding-vertical: 8px;     /* vertical padding inside each row */
    --row-padding-horizontal: 16px;   /* horizontal padding inside each row */
    --row-gap: 14px;                  /* grid gap between columns */
    --number-width: 45px;             /* width of number badge column */
    --number-height: 28px;            /* height of number badge */
  }

  /* ===== CONTAINER & LAYOUT ===== */
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

  .search-clear-btn:hover { color: #999; }
  .search-box input:not(:placeholder-shown) ~ .search-clear-btn { display: block; }

  /* ===== CARD HEADER / BANNER (match unit_organisasi) ===== */
  .card-header {
    background: linear-gradient(135deg, #0b2545 0%, #0b58a6 100%);
    padding: var(--header-padding-vertical) 16px;
  }
  .card-header h5 {
    color: #fff;
    font-weight: 500;
    font-size: 16px;
    margin: 0;
  }

  /* placeholder right area (keeps header layout same as unit_organisasi) */
  .header-placeholder { width: 140px; height: 36px; }

  /* ===== UNIT ROW (identical to unit_organisasi) ===== */
  .unit-list { display: flex; flex-direction: column; }

  .unit-row {
    display: grid;
    grid-template-columns: var(--number-width) 90px 1fr 150px auto;
    gap: var(--row-gap);
    align-items: center;
    padding: var(--row-padding-vertical) var(--row-padding-horizontal);
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
    background: #fff;
  }

  .unit-row:last-child { border-bottom: none; }

  .unit-row[data-level="1"] { padding-left: 16px; background-color: #fafbfc; font-weight: 400; }
  .unit-row[data-level="2"] { padding-left: 36px; background-color: #f8f9fb; }
  .unit-row[data-level="3"] { padding-left: 56px; background-color: #ffffff; }

  /* ===== NUMBER BADGE (left) - make it like unit_organisasi .unit-no ===== */
  .unit-no {
    font-weight: 400;
    color: #0b58a6;
    text-align: center;
    font-size: 12px;
    min-width: var(--number-width);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #eaf6ff;
    border-radius: 6px;
    height: var(--number-height);
  }

  /* ===== unit-kode kept to match structure (second column) but we leave it blank for PD */
  .unit-kode {
    background: #e3f2fd;
    color: #0b58a6;
    padding: 6px 10px;
    border-radius: 4px;
    font-weight: 400;
    font-size: 12px;
    text-align: center;
    min-width: 90px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 28px;
  }

  .unit-info { display: flex; flex-direction: column; gap: 3px; }
  .unit-nama { font-weight: 400; color: #2c3e50; font-size: 13px; line-height: 1.4; }

  /* ===== UNIT ACTIONS and BUTTONS - copied from unit_organisasi to ensure identical style ===== */
  .unit-actions {
    display: flex;
    gap: 8px;
    justify-content: flex-end;
  }

  .btn-action {
    border: none;
    cursor: pointer;
    padding: 6px 12px;
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

  /* ===== Optional small adjustments for exact pixel parity on narrow screens ===== */
  @media (max-width: 768px) {
    .search-container { justify-content: flex-start; flex-direction: column; }
    .unit-row { grid-template-columns: 45px 80px 1fr 120px auto; gap: 10px; padding: 10px; }
    .unit-kode { min-width: 80px; height: 26px; }
    .unit-no { min-width: 40px; height: 26px; }
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building"></i> Master Perangkat Daerah</h5>
        <div class="header-placeholder" aria-hidden="true"></div>
      </div>

      <div class="card-body">
        @if($perangkat->count() > 0)
          <div class="search-container">
            <div class="search-box">
              <input type="text" id="searchInput" placeholder="Cari perangkat daerah..." autocomplete="off" />
              <button class="search-clear-btn" onclick="clearSearch()" title="Hapus pencarian"><i class="fas fa-times"></i></button>
            </div>
          </div>

          <div class="unit-list">
            @foreach($perangkat as $index => $pd)
              <div class="unit-row" data-level="1">
                <div class="unit-no">{{ $index + 1 }}</div>
                <div class="unit-kode">&nbsp;</div>
                <div class="unit-info">
                  <div class="unit-nama">{{ $pd->nama }}</div>
                </div>
                <div class="unit-actions">
                  <a href="{{ route('perangkat_daerah.edit', $pd->id) }}" class="btn-action btn-edit" title="Edit"><i class="fas fa-edit"></i> Edit</a>

                  <form action="{{ route('perangkat_daerah.destroy', $pd->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus perangkat daerah ini?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete" title="Hapus"><i class="fas fa-trash"></i> Hapus</button>
                  </form>
                </div>
              </div>
            @endforeach
          </div>

        @else
          <div class="alert alert-info"><i class="fas fa-info-circle"></i> Belum ada data Perangkat Daerah</div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    searchInput.addEventListener('input', function(e) {
      const q = e.target.value.trim().toLowerCase();
      document.querySelectorAll('.unit-list .unit-row').forEach(row => {
        const name = (row.querySelector('.unit-nama')?.textContent || '').trim().toLowerCase();
        row.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
    });
  });

  function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    searchInput.value = '';
    searchInput.focus();
    document.querySelectorAll('.unit-list .unit-row').forEach(r => r.style.display = '');
  }
</script>
@endsection