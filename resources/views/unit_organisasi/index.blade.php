@extends('layouts.app')

@section('title', 'Master Unit Organisasi')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-sitemap"></i> Master Unit Organisasi</h5>
                <a href="{{ route('unit_organisasi.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Tambah UNOR
                </a>
            </div>
            <div class="card-body">
                @if($unitOrganisasi->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>KODE</th>
                                    <th>NAMA UNOR</th>
                                    <th>PERANGKAT DAERAH</th>
                                    <th>UNOR ATASAN</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($unitOrganisasi as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><span class="badge bg-info">{{ $item->kode }}</span></td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ $item->perangkatDaerah->nama ?? '-' }}</td>
                                        <td>{{ $item->unor_atasan ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('unit_organisasi.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('unit_organisasi.destroy', $item->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="confirmDelete(event)">
                                                    <i class="fas fa-trash"></i> Hapus
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
                        <i class="fas fa-info-circle"></i> Belum ada data Unit Organisasi
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection