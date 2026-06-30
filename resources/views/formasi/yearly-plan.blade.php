@extends('layouts.app')

@section('title', 'Rencana Formasi Tahunan')

@section('css')
<style>
    .yearly-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .yearly-card h6 {
        border-bottom: 3px solid #3498db;
        padding-bottom: 10px;
        margin-bottom: 15px;
        color: #2c3e50;
    }
    
    .formasi-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
    }
    
    @media (max-width: 768px) {
        .formasi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .formasi-item {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 6px;
    }
    
    .formasi-item label {
        font-size: 12px;
        color: #7f8c8d;
        margin-bottom: 5px;
    }
    
    .formasi-item .value {
        font-size: 20px;
        font-weight: 700;
        color: #3498db;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Perencanaan Formasi ASN Tahunan (2027-2032)</h5>
            </div>
            <div class="card-body">
                @foreach($tahunList as $tahun)
                    <div class="yearly-card">
                        <h6><i class="fas fa-calendar"></i> Tahun {{ $tahun }}</h6>
                        <div class="formasi-grid">
                            <div class="formasi-item">
                                <label>JPT</label>
                                <div class="value" style="color: #3498db;">{{ $data[$tahun]->jpt ?? 0 }}</div>
                            </div>
                            <div class="formasi-item">
                                <label>ADM & PENGAWAS</label>
                                <div class="value" style="color: #27ae60;">{{ $data[$tahun]->adm_pengawas ?? 0 }}</div>
                            </div>
                            <div class="formasi-item">
                                <label>MUTASI</label>
                                <div class="value" style="color: #f39c12;">{{ $data[$tahun]->mutasi ?? 0 }}</div>
                            </div>
                            <div class="formasi-item">
                                <label>CPNS</label>
                                <div class="value" style="color: #e74c3c;">{{ $data[$tahun]->cpns ?? 0 }}</div>
                            </div>
                            <div class="formasi-item">
                                <label>PPPK</label>
                                <div class="value" style="color: #9b59b6;">{{ $data[$tahun]->pppk ?? 0 }}</div>
                            </div>
                        </div>
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #ecf0f1;">
                            <div style="text-align: right;">
                                <strong>TOTAL KEBUTUHAN TAHUN {{ $tahun }}: </strong>
                                <span style="font-size: 18px; font-weight: 700; color: #2c3e50;">
                                    {{ ($data[$tahun]->jpt ?? 0) + ($data[$tahun]->adm_pengawas ?? 0) + ($data[$tahun]->mutasi ?? 0) + ($data[$tahun]->cpns ?? 0) + ($data[$tahun]->pppk ?? 0) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="yearly-card" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white;">
                    <h6 style="color: white; border-bottom-color: white;"><i class="fas fa-plus-circle"></i> SISA KEBUTUHAN ASN 2032 (Akumulasi 2027-2031)</h6>
                    <div class="formasi-grid">
                        <div class="formasi-item" style="background: rgba(255,255,255,0.2); color: white;">
                            <label style="color: rgba(255,255,255,0.8);">JPT</label>
                            <div class="value" style="color: #3498db;">{{ $sisaKebutuhan->jpt ?? 0 }}</div>
                        </div>
                        <div class="formasi-item" style="background: rgba(255,255,255,0.2); color: white;">
                            <label style="color: rgba(255,255,255,0.8);">ADM & PENGAWAS</label>
                            <div class="value" style="color: #27ae60;">{{ $sisaKebutuhan->adm_pengawas ?? 0 }}</div>
                        </div>
                        <div class="formasi-item" style="background: rgba(255,255,255,0.2); color: white;">
                            <label style="color: rgba(255,255,255,0.8);">MUTASI</label>
                            <div class="value" style="color: #f39c12;">{{ $sisaKebutuhan->mutasi ?? 0 }}</div>
                        </div>
                        <div class="formasi-item" style="background: rgba(255,255,255,0.2); color: white;">
                            <label style="color: rgba(255,255,255,0.8);">CPNS</label>
                            <div class="value" style="color: #e74c3c;">{{ $sisaKebutuhan->cpns ?? 0 }}</div>
                        </div>
                        <div class="formasi-item" style="background: rgba(255,255,255,0.2); color: white;">
                            <label style="color: rgba(255,255,255,0.8);">PPPK</label>
                            <div class="value" style="color: #9b59b6;">{{ $sisaKebutuhan->pppk ?? 0 }}</div>
                        </div>
                    </div>
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid rgba(255,255,255,0.3); text-align: right;">
                        <strong style="font-size: 16px;">TOTAL SISA KEBUTUHAN:</strong>
                        <div style="font-size: 24px; font-weight: 700; margin-top: 10px;">
                            {{ ($sisaKebutuhan->jpt ?? 0) + ($sisaKebutuhan->adm_pengawas ?? 0) + ($sisaKebutuhan->mutasi ?? 0) + ($sisaKebutuhan->cpns ?? 0) + ($sisaKebutuhan->pppk ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection