@extends('layouts.app')

@section('title', 'Gap Analysis ASN')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Gap Analysis Kebutuhan ASN </h5>
                <a href="{{ route('laporan.export-gap-excel') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>K:</strong> (Kebutuhan Pegawai)
                    <strong style="margin-left: 20px;">B:</strong> Bezetting (Posisi yang sudah terisi)
                    <strong style="margin-left: 20px;">+/-:</strong> Gap
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover datatable">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>PERANGKAT DAERAH</th>
                                <th>UNIT ORGANISASI</th>
                                <th>KODE JABATAN</th>
                                <th>NAMA JABATAN</th>
                                <th>K</th>
                                <th>B</th>
                                <th>+/-</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($jabatan as $item)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $item->unitOrganisasi->perangkatDaerah->nama ?? '-' }}</td>
                                    <td>{{ $item->unitOrganisasi->nama ?? '-' }}</td>
                                    <td><span class="badge bg-primary">{{ $item->kode }}</span></td>
                                    <td>{{ $item->nama }}</td>

                                    {{-- K: kebutuhan (k) --}}
                                    <td class="text-center"><span class="badge bg-warning fs-6">{{ $item->k }}</span></td>

                                    {{-- B: bezetting (b) --}}
                                    <td class="text-center"><span class="badge bg-info fs-6">{{ $item->b }}</span></td>

                                    {{-- Gap: B - K; tampilkan tanda + untuk positif --}}
                                    <td class="text-center">
                                        @if($item->gap > 0)
                                            <span class="badge bg-success fs-6">+{{ $item->gap }}</span>
                                        @elseif($item->gap < 0)
                                            <span class="badge bg-danger fs-6">{{ $item->gap }}</span>
                                        @else
                                            <span class="badge bg-secondary fs-6">0</span>
                                        @endif
                                    </td>

                                    <td>
                                        @if($item->gap < 0)
                                            <span class="badge bg-danger">Kekurangan</span>
                                        @elseif($item->gap > 0)
                                            <span class="badge bg-secondary">Kelebihan</span>
                                        @else
                                            <span class="badge bg-success">Terpenuhi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary Stats -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="p-3 bg-danger text-white rounded">
                            <h6>Jabatan dengan Kekurangan</h6>
                            <h4>{{ $jabatan->where('gap', '<', 0)->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-success text-white rounded">
                            <h6>Jabatan Terpenuhi</h6>
                            <h4>{{ $jabatan->where('gap', 0)->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-secondary text-white rounded">
                            <h6>Jabatan dengan Kelebihan</h6>
                            <h4>{{ $jabatan->where('gap', '>', 0)->count() }}</h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="p-3 bg-primary text-white rounded">
                            <h6>Total Gap (B - K)</h6>
                            <h4>{{ $jabatan->sum('gap') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
</div>
@endsection