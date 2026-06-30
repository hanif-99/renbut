@extends('layouts.app')

@section('title', 'Import Data Excel')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-upload"></i> Import Data dari Excel</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Catatan Penting:</strong>
                    <ul class="mb-0" style="margin-top: 10px;">
                        <li>File harus dalam format .xlsx atau .xls</li>
                        <li>Pastikan data sudah diperiksa sebelum mengimport</li>
                        <li>Proses import tidak dapat dibatalkan setelah dijalankan</li>
                    </ul>
                </div>

                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label for="file" class="form-label">Pilih File Excel <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Format: .xlsx atau .xls (Maksimal 5MB)</small>
                    </div>

                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> Struktur File Excel</h6>
                        <p>File Excel harus memiliki sheet-sheet berikut:</p>
                        <ul>
                            <li><strong>Perangkat Daerah:</strong> KODE, NAMA</li>
                            <li><strong>Unit Organisasi:</strong> KODE, NAMA, PERANGKAT_DAERAH_ID, UNOR_ATASAN</li>
                            <li><strong>Jabatan:</strong> KODE, NAMA, UNIT_ORGANISASI_ID, JENIS_JABATAN_ID, JENJANG_ID, KJ, B, K</li>
                            <li><strong>Formasi 2027-2032:</strong> JABATAN_ID, JPT, ADM_PENGAWAS, MUTASI, CPNS, PPPK</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Upload & Import
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection