@extends('layouts.app')

@section('title', 'Dashboard')

@section('css')
<style>
    .chart-container {
        position: relative;
        height: 300px;
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="kpi-card">
            <h3><i class="fas fa-building"></i> Total Perangkat Daerah</h3>
            <div class="value">{{ $totalPerangkat }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <h3><i class="fas fa-sitemap"></i> Total Unit Organisasi</h3>
            <div class="value">{{ $totalUnor }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <h3><i class="fas fa-briefcase"></i> Total Jabatan</h3>
            <div class="value">{{ $totalJabatan }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="kpi-card">
            <h3><i class="fas fa-users"></i> Total Kebutuhan</h3>
            <div class="value">{{ $formasiSummary->total_jpt + $formasiSummary->total_adm + $formasiSummary->total_mutasi + $formasiSummary->total_cpns + $formasiSummary->total_pppk }}</div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar"></i> Ringkasan Kebutuhan ASN</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>JPT</h6>
                            <h4 class="text-primary">{{ $formasiSummary->total_jpt ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>ADM & PENGAWAS</h6>
                            <h4 class="text-success">{{ $formasiSummary->total_adm ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>MUTASI</h6>
                            <h4 class="text-info">{{ $formasiSummary->total_mutasi ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>CPNS</h6>
                            <h4 class="text-warning">{{ $formasiSummary->total_cpns ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-light rounded">
                            <h6>PPPK</h6>
                            <h4 class="text-danger">{{ $formasiSummary->total_pppk ?? 0 }}</h4>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center p-3 bg-primary text-white rounded">
                            <h6>TOTAL</h6>
                            <h4>{{ $formasiSummary->total_jpt + $formasiSummary->total_adm + $formasiSummary->total_mutasi + $formasiSummary->total_cpns + $formasiSummary->total_pppk }}</h4>
                        </div>
                    </div>
                </div>

                <div class="chart-container mt-4">
                    <canvas id="formasiChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-line-chart"></i> Kebutuhan ASN per Tahun</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="tahunChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    // Chart Formasi (Pie)
    const formasiCtx = document.getElementById('formasiChart').getContext('2d');
    new Chart(formasiCtx, {
        type: 'doughnut',
        data: {
            labels: ['JPT', 'ADM & PENGAWAS', 'MUTASI', 'CPNS', 'PPPK'],
            datasets: [{
                data: [
                    {{ $formasiSummary->total_jpt ?? 0 }},
                    {{ $formasiSummary->total_adm ?? 0 }},
                    {{ $formasiSummary->total_mutasi ?? 0 }},
                    {{ $formasiSummary->total_cpns ?? 0 }},
                    {{ $formasiSummary->total_pppk ?? 0 }}
                ],
                backgroundColor: [
                    '#3498db',
                    '#27ae60',
                    '#f39c12',
                    '#e74c3c',
                    '#9b59b6'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Chart per Tahun (Line)
    const tahunCtx = document.getElementById('tahunChart').getContext('2d');
    new Chart(tahunCtx, {
        type: 'line',
        data: {
            labels: {!! $formasiByTahun->pluck('tahun')->toJson() !!},
            datasets: [
                {
                    label: 'JPT',
                    data: {!! $formasiByTahun->pluck('jpt')->toJson() !!},
                    borderColor: '#3498db',
                    backgroundColor: 'rgba(52, 152, 219, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'ADM & PENGAWAS',
                    data: {!! $formasiByTahun->pluck('adm_pengawas')->toJson() !!},
                    borderColor: '#27ae60',
                    backgroundColor: 'rgba(39, 174, 96, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'MUTASI',
                    data: {!! $formasiByTahun->pluck('mutasi')->toJson() !!},
                    borderColor: '#f39c12',
                    backgroundColor: 'rgba(243, 156, 18, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'CPNS',
                    data: {!! $formasiByTahun->pluck('cpns')->toJson() !!},
                    borderColor: '#e74c3c',
                    backgroundColor: 'rgba(231, 76, 60, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'PPPK',
                    data: {!! $formasiByTahun->pluck('pppk')->toJson() !!},
                    borderColor: '#9b59b6',
                    backgroundColor: 'rgba(155, 89, 182, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection