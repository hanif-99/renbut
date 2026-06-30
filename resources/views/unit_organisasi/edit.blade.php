@extends('layouts.app')

@section('title', 'Edit Unit Organisasi')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Unit Organisasi</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('unit_organisasi.update', $unitOrganisasi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="kode" class="form-label">KODE <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode', $unitOrganisasi->kode) }}" required>
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">NAMA UNIT ORGANISASI <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $unitOrganisasi->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="perangkat_daerah_id" class="form-label">PERANGKAT DAERAH <span class="text-danger">*</span></label>
                        <select class="form-select @error('perangkat_daerah_id') is-invalid @enderror" id="perangkat_daerah_id" name="perangkat_daerah_id" required>
                            <option value="">-- Pilih Perangkat Daerah --</option>
                            @foreach($perangkatDaerah as $item)
                                <option value="{{ $item->id }}" @selected(old('perangkat_daerah_id', $unitOrganisasi->perangkat_daerah_id) == $item->id)>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('perangkat_daerah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unor_atasan" class="form-label">UNOR ATASAN</label>
                        <input type="text" class="form-control @error('unor_atasan') is-invalid @enderror" id="unor_atasan" name="unor_atasan" value="{{ old('unor_atasan', $unitOrganisasi->unor_atasan) }}">
                        @error('unor_atasan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('unit_organisasi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection