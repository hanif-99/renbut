@extends('layouts.app')

@section('title', 'Peta Jabatan (Organogram)')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-semibold mb-4">Peta Jabatan (Bagan Organisasi)</h1>

  <div class="mb-4 flex items-center gap-2">
    <label for="pd-select" class="font-medium">Pilih Perangkat Daerah:</label>
    <select id="pd-select" class="border rounded px-3 py-1" data-default-pd="{{ $perangkats->first()->id ?? '' }}">
      <option value="">-- Semua Perangkat Daerah --</option>
      @foreach($perangkats as $pd)
        <option value="{{ $pd->id }}">{{ $pd->nama }}</option>
      @endforeach
    </select>

    <button
      type="button"
      class="ml-2 btn btn-sm btn-primary"
      id="btn-load-organogram"
    >
      Muat
    </button>
  </div>

  <div id="orgchart" style="width:100%; height: 70vh; border: 1px solid #e5e7eb; border-radius: 6px;"></div>
</div>

<!-- Modal detail jabatan (Bootstrap 5) -->
<div class="modal fade" id="orgchartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Jabatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="orgchartModalContent">
          <p><strong>Nama:</strong> <span id="jab-nama">-</span></p>
          <p><strong>Kode:</strong> <span id="jab-kode">-</span></p>
          <p><strong>Perangkat Daerah:</strong> <span id="jab-pd">-</span></p>
          <p><strong>Unit Organisasi:</strong> <span id="jab-unit">-</span></p>
          <p><strong>Kebutuhan (K):</strong> <span id="jab-k">0</span></p>
          <p><strong>Bezetting (B):</strong> <span id="jab-b">0</span></p>
          <p><strong>Gap (B - K):</strong> <span id="jab-gap">0</span></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
  @vite('resources/js/organogram.js')
@endpush