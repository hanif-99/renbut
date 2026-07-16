@extends('layouts.app')

@section('title', 'Laporan Ringkasan')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Laporan Ringkasan Formasi ASN</h5>
                <div class="d-flex gap-2">
                    <form method="GET" class="d-flex gap-2">
                        <select name="tahun" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                            @foreach($tahunList as $t)
                                <option value="{{ $t }}" @selected($tahun == $t)>Tahun {{ $t }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('laporan.export-excel', ['tahun' => $tahun]) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <a href="{{ route('laporan.export-pdf', ['tahun' => $tahun]) }}" class="btn btn-sm btn-danger">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> Laporan Formasi ASN Tahun <strong>{{ $tahun }}</strong>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 style="color: #7f8c8d; margin-bottom: 10px;">JPT</h6>
                            <h3 style="color: #3498db; margin: 0;">{{ $summary->jpt ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 style="color: #7f8c8d; margin-bottom: 10px;">ADM & PENGAWAS</h6>
                            <h3 style="color: #27ae60; margin: 0;">{{ $summary->adm_pengawas ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 style="color: #7f8c8d; margin-bottom: 10px;">MUTASI</h6>
                            <h3 style="color: #f39c12; margin: 0;">{{ $summary->mutasi ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 style="color: #7f8c8d; margin-bottom: 10px;">CPNS</h6>
                            <h3 style="color: #e74c3c; margin: 0;">{{ $summary->cpns ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 style="color: #7f8c8d; margin-bottom: 10px;">PPPK</h6>
                            <h3 style="color: #9b59b6; margin: 0;">{{ $summary->pppk ?? 0 }}</h3>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-primary text-white rounded">
                            <h6 style="margin-bottom: 10px;">TOTAL</h6>
                            <h3 style="margin: 0;">{{ ($summary->jpt ?? 0) + ($summary->adm_pengawas ?? 0) + ($summary->mutasi ?? 0) + ($summary->cpns ?? 0) + ($summary->pppk ?? 0) }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Detail Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>PERANGKAT DAERAH</th>
                                <th>UNIT ORGANISASI</th>
                                <th>JABATAN</th>
                                <th>JPT</th>
                                <th>ADM & PENGAWAS</th>
                                <th>MUTASI</th>
                                <th>CPNS</th>
                                <th>PPPK</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalJpt = 0;
                                $totalAdm = 0;
                                $totalMutasi = 0;
                                $totalCpns = 0;
                                $totalPppk = 0;
                            @endphp
                            @foreach($detailByPerangkat as $perangkatId => $formasiItems)
                                @php
                                    $perangkat = $formasiItems->first()->jabatan->unitOrganisasi->perangkatDaerah;
                                @endphp
                                <tr style="background-color: #ecf0f1; font-weight: 600;">
                                    <td colspan="9">{{ $perangkat->nama }}</td>
                                </tr>
                                @foreach($formasiItems as $formasi)
                                    <tr>
                                        <td></td>
                                        <td>{{ $formasi->jabatan->unitOrganisasi->nama }}</td>
                                        <td>{{ $formasi->jabatan->nama }}</td>
                                        <td class="text-end"><span class="badge bg-info">{{ $formasi->jpt }}</span></td>
                                        <td class="text-end"><span class="badge bg-success">{{ $formasi->adm_pengawas }}</span></td>
                                        <td class="text-end"><span class="badge bg-warning">{{ $formasi->mutasi }}</span></td>
                                        <td class="text-end"><span class="badge bg-danger">{{ $formasi->cpns }}</span></td>
                                        <td class="text-end"><span class="badge bg-secondary">{{ $formasi->pppk }}</span></td>
                                        <td class="text-end"><span class="badge bg-dark">{{ $formasi->jpt + $formasi->adm_pengawas + $formasi->mutasi + $formasi->cpns + $formasi->pppk }}</span></td>
                                    </tr>
                                    @php
                                        $totalJpt += $formasi->jpt;
                                        $totalAdm += $formasi->adm_pengawas;
                                        $totalMutasi += $formasi->mutasi;
                                        $totalCpns += $formasi->cpns;
                                        $totalPppk += $formasi->pppk;
                                    @endphp
                                @endforeach
                            @endforeach
                            <tr style="background-color: #2c3e50; color: white; font-weight: 700;">
                                <td colspan="3">TOTAL</td>
                                <td class="text-end">{{ $totalJpt }}</td>
                                <td class="text-end">{{ $totalAdm }}</td>
                                <td class="text-end">{{ $totalMutasi }}</td>
                                <td class="text-end">{{ $totalCpns }}</td>
                                <td class="text-end">{{ $totalPppk }}</td>
                                <td class="text-end">{{ $totalJpt + $totalAdm + $totalMutasi + $totalCpns + $totalPppk }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection