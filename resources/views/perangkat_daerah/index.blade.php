@extends('layouts.app')

@section('title', 'Master Perangkat Daerah')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-building"></i> Master Perangkat Daerah</h5>
                <a href="{{ route('perangkat_daerah.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Tambah Perangkat
                </a>
            </div>
            <div class="card-body">
                @if($perangkat->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover datatable">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>KODE</th>
                                    <th>NAMA PERANGKAT DAERAH</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($perangkat as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td><span class="badge bg-primary">{{ $item->kode }}</span></td>
                                        <td>{{ $item->nama }}</td>
                                        <td>
                                            <a href="{{ route('perangkat_daerah.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <form action="{{ route('perangkat_daerah.destroy', $item->id) }}" method="POST" style="display:inline;">
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
                        <i class="fas fa-info-circle"></i> Belum ada data Perangkat Daerah
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection