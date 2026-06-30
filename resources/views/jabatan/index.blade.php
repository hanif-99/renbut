@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-briefcase"></i> Master Jabatan</h5>
                <a href="{{ route('jabatan.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Tambah Jabatan
                </a>
            </div>
            <div class="card-body">
                @if($jabatan->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>KODE</th>
                                    <th>NAMA JABATAN</th>
                                    <th>UNIT ORGANISASI</th>
                                    <th>JENIS</th>
                                    <th>JENJANG</th>
                                    <th>K</th>
                                    <th>B</th>
                                    <th>+/-</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jabatan as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><span class="badge bg-success">{{ $item->kode }}</span></td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->unitOrganisasi->nama ?? '-' }}</td>
                                        <td>{{ $item->jenisJabatan->nama ?? '-' }}</td>
                                        <td>{{ $item->jenjang->nama ?? '-' }}</td>
                                        <td><span class="badge bg-warning">{{ $item->b }}</span></td>
                                        <td><span class="badge bg-info">{{ $item->k }}</span></td>
                                        <td>
                                            @if($item->b - $item->k > 0)
                                                <span class="badge bg-danger">+{{ $item->b - $item->k }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $item->b - $item->k }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('jabatan.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('jabatan.destroy', $item->id) }}" method="POST" style="display:inline;">
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
                        <i class="fas fa-info-circle"></i> Belum ada data Jabatan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection