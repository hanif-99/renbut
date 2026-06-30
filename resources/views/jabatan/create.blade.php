@extends('layouts.app')

@section('title', 'Tambah Jabatan')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus"></i> Tambah Jabatan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('jabatan.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="kode" class="form-label">KODE <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode') }}" required>
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">NAMA JABATAN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="unit_organisasi_id" class="form-label">UNIT ORGANISASI <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_organisasi_id') is-invalid @enderror" id="unit_organisasi_id" name="unit_organisasi_id" required>
                            <option value="">-- Pilih Unit Organisasi --</option>
                            @foreach($unitOrganisasi as $item)
                                <option value="{{ $item->id }}" @selected(old('unit_organisasi_id') == $item->id)>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('unit_organisasi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenis_jabatan_id" class="form-label">JENIS JABATAN</label>
                        <select class="form-select @error('jenis_jabatan_id') is-invalid @enderror" id="jenis_jabatan_id" name="jenis_jabatan_id">
                            <option value="">-- Pilih Jenis Jabatan --</option>
                            @foreach($jenisJabatan as $item)
                                <option value="{{ $item->id }}" @selected(old('jenis_jabatan_id') == $item->id)>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenis_jabatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenjang_id" class="form-label">JENJANG</label>
                        <select class="form-select @error('jenjang_id') is-invalid @enderror" id="jenjang_id" name="jenjang_id">
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($jenjang as $item)
                                <option value="{{ $item->id }}" @selected(old('jenjang_id') == $item->id)>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenjang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kj" class="form-label">KJ (Kelas Jabatan)</label>
                        <input type="text" class="form-control @error('kj') is-invalid @enderror" id="kj" name="kj" value="{{ old('kj') }}">
                        @error('kj')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="b" class="form-label">K (Kebutuhan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('b') is-invalid @enderror" id="b" name="b" value="{{ old('b', 0) }}" min="0" required>
                                @error('b')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="k" class="form-label">B (Bezetting) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('k') is-invalid @enderror" id="k" name="k" value="{{ old('k', 0) }}" min="0" required>
                                @error('k')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection