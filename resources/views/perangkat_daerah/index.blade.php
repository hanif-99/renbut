{{-- resources/views/perangkat_daerah/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Perangkat Daerah')

@section('css')
<style>
  /* CSS variables for easy tuning */
  :root{
    --header-padding-vertical: 5px;   /* controls banner (card-header) vertical padding / visual height */
    --row-padding-vertical: 8px;       /* controls vertical padding inside each row */
    --row-padding-horizontal: 16px;    /* controls horizontal padding inside each row */
    --row-gap: 12px;                   /* grid gap between columns inside a row */
    --number-width: 45px;              /* width/min-width of number column */
    --number-height: 28px;             /* height of number bubble */
  }

  /* ===== CONTAINER & LAYOUT ===== */
  .search-container {
    margin-bottom: 11px;
    display: flex;
    gap: 10px;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
  }

  .search-box {
    flex: 0 1 240px;
    position: relative;
  }

  .search-box input {
    width: 100%;
    padding: 5px 35px 10px 14px;
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

  /* ===== CARD HEADER / BANNER =====
     Adjust banner height:
     - Change --header-padding-vertical to increase/decrease banner vertical size.
  */
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

  /* keep a right-side placeholder to match unit_organisasi header layout */
  .header-placeholder {
    width: 140px;
    height: 36px; /* matches typical button height */
  }

  /* ===== ROW LIST (make spacing like Unit Organisasi but compacted) =====
     Adjust spacing:
     - Change --row-padding-vertical to increase/decrease vertical padding of each row.
     - Change --row-gap to change gap between columns.
  */
  .unit-list {
    display: flex;
    flex-direction: column;
  }

  .unit-row {
    display: grid;
    /* columns: number | name | actions */
    grid-template-columns: var(--number-width) 1fr 150px;
    gap: var(--row-gap);
    align-items: center;
    padding: var(--row-padding-vertical) var(--row-padding-horizontal);
    border-bottom: 1px solid #f0f0f0;
    font-size: 13px;
    background: #fff;
  }

  .unit-row:last-child { border-bottom: none; }

  /* indentation levels kept but default level=1 */
  .unit-row[data-level="1"] { background-color: #fafbfc; }
  .unit-row[data-level="2"] { background-color: #f8f9fb; padding-left: calc(var(--row-padding-horizontal) + 20px); }
  .unit-row[data-level="3"] { background-color: #ffffff; padding-left: calc(var(--row-padding-horizontal) + 40px); }

  /* ===== number column (left) =====
     - Controls numeric bubble look; adjust --number-width and --number-height
  */
  .unit-number {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: var(--number-width);
    width: var(--number-width);
    height: var(--number-height);
    background: #eaf6ff;  /* subtle bubble similar to unit_organisasi color */
    color: #0b58a6;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
  }

  /* ===== name column ===== */
  .unit-name {
    color: #2c3e50;
    font-size: 13px;
    line-height: 1.4;
  }

  /* ===== actions (right) ===== */
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
    gap: 6px;
    font-weight: 500;
  }

  .btn-edit { background-color: #ffc107; color: #000; }
  .btn-edit:hover { background-color: #ffb300; }

  .btn-delete { background-color: #dc3545; color: #fff; }
  .btn-delete:hover { background-color: #c82333; }

  /* responsive adjustments */
  @media (max-width: 768px) {
    .search-container { justify-content: flex-start; flex-direction: column; }
    .unit-row { grid-template-columns: 45px 1fr auto; gap: 10px; padding: 10px; }
  }
</style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="card">
      {{-- header/banner with smaller height controlled by --header-padding-vertical --}}
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-building"></i> Master Perangkat Daerah</h5>

        {{-- placeholder to keep header layout identical (no visible Add button) --}}
        <div class="header-placeholder" aria-hidden="true"></div>
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

          <div class="unit-list" id="opdList">
            @foreach($perangkat as $i => $opd)
              <div class="unit-row" data-level="1">
                <div class="unit-number">{{ $i + 1 }}</div>

                <div class="unit-name">{{ $opd->nama }}</div>

                <div class="unit-actions">
                  <a href="{{ route('perangkat_daerah.edit', $opd->id) }}" class="btn-action btn-edit"><i class="fas fa-edit"></i> Edit</a>

                  <form action="{{ route('perangkat_daerah.destroy', $opd->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-action btn-delete" onclick="return confirm('Yakin ingin menghapus perangkat daerah ini?')"><i class="fas fa-trash"></i> Hapus</button>
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
  if (searchInput) {
    searchInput.addEventListener('input', function(e) {
      const q = e.target.value.trim().toLowerCase();
      document.querySelectorAll('.unit-list .unit-row').forEach(row => {
        const name = (row.querySelector('.unit-name')?.textContent || '').trim().toLowerCase();
        row.style.display = (!q || name.includes(q)) ? '' : 'none';
      });
    });
  }
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