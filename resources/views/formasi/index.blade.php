@extends('layouts.app')

@section('title', 'Perencanaan Formasi ASN')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Perencanaan Formasi ASN</h5>
                <div class="d-flex gap-2">
                    <form method="GET" action="{{ route('formasi.index') }}" class="d-flex gap-2">
                        <select name="tahun" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                            @foreach($tahunList as $t)
                                <option value="{{ $t }}" @selected($tahun == $t)>Tahun {{ $t }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('formasi.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Tambah Formasi
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($formasi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>KODE</th>
                                    <th>NAMA JABATAN</th>
                                    <th>UNIT ORGANISASI</th>
                                    <th>JPT</th>
                                    <th>ADM & PENGAWAS</th>
                                    <th>MUTASI</th>
                                    <th>CPNS</th>
                                    <th>PPPK</th>
                                    <th>TOTAL</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($formasi as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><span class="badge bg-primary">{{ $item->jabatan->kode }}</span></td>
                                        <td>{{ $item->jabatan->nama }}</td>
                                        <td>{{ $item->jabatan->unitOrganisasi->nama ?? '-' }}</td>
                                        <td><span class="badge bg-info">{{ $item->jpt }}</span></td>
                                        <td><span class="badge bg-success">{{ $item->adm_pengawas }}</span></td>
                                        <td><span class="badge bg-warning">{{ $item->mutasi }}</span></td>
                                        <td><span class="badge bg-danger">{{ $item->cpns }}</span></td>
                                        <td><span class="badge bg-secondary">{{ $item->pppk }}</span></td>
                                        <td><span class="badge bg-dark">{{ $item->jpt + $item->adm_pengawas + $item->mutasi + $item->cpns + $item->pppk }}</span></td>
                                        <td>
                                            <a href="{{ route('formasi.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('formasi.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="confirmDelete(event)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Belum ada data Formasi untuk tahun {{ $tahun }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection