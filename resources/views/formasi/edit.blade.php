@extends('layouts.app')

@section('title', 'Edit Formasi ASN')

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Formasi ASN</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('formasi.update', $formasi->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jabatan_id" class="form-label">JABATAN <span class="text-danger">*</span></label>
                                <select class="form-select @error('jabatan_id') is-invalid @enderror" id="jabatan_id" name="jabatan_id" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    @foreach($jabatan as $item)
                                        <option value="{{ $item->id }}" @selected(old('jabatan_id', $formasi->jabatan_id) == $item->id)>{{ $item->kode }} - {{ $item->nama }}</option>
                                    @endforeach
                                </select>
                                @error('jabatan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tahun" class="form-label">TAHUN <span class="text-danger">*</span></label>
                                <select class="form-select @error('tahun') is-invalid @enderror" id="tahun" name="tahun" required>
                                    <option value="">-- Pilih Tahun --</option>
                                    @foreach($tahunList as $t)
                                        <option value="{{ $t }}" @selected(old('tahun', $formasi->tahun) == $t)>{{ $t }}</option>
                                    @endforeach
                                </select>
                                @error('tahun')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="jpt" class="form-label">JPT <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('jpt') is-invalid @enderror" id="jpt" name="jpt" value="{{ old('jpt', $formasi->jpt) }}" min="0" required>
                                @error('jpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="adm_pengawas" class="form-label">ADM & PENGAWAS <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('adm_pengawas') is-invalid @enderror" id="adm_pengawas" name="adm_pengawas" value="{{ old('adm_pengawas', $formasi->adm_pengawas) }}" min="0" required>
                                @error('adm_pengawas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="mutasi" class="form-label">MUTASI <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('mutasi') is-invalid @enderror" id="mutasi" name="mutasi" value="{{ old('mutasi', $formasi->mutasi) }}" min="0" required>
                                @error('mutasi')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cpns" class="form-label">CPNS <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('cpns') is-invalid @enderror" id="cpns" name="cpns" value="{{ old('cpns', $formasi->cpns) }}" min="0" required>
                                @error('cpns')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pppk" class="form-label">PPPK <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('pppk') is-invalid @enderror" id="pppk" name="pppk" value="{{ old('pppk', $formasi->pppk) }}" min="0" required>
                                @error('pppk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('formasi.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection