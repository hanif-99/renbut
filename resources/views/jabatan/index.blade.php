@extends('layouts.app')

@section('title', 'Master Jabatan')

@section('css')
<style>
    .jabatan-parent {
        background-color: #f8f9fa;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
    }

    .jabatan-parent:hover {
        background-color: #e9ecef;
    }

    .jabatan-child {
        display: none;
    }

    .jabatan-child.show {
        display: table-row;
    }

    .jabatan-parent .toggle-icon {
        display: inline-block;
        width: 16px;
        text-align: center;
        margin-right: 8px;
        transition: transform 0.2s;
        color: #0066cc;
    }

    .jabatan-parent.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    .jabatan-child td:first-child {
        padding-left: 50px !important;
        border-left: 3px solid #dee2e6;
        position: relative;
    }

    .jabatan-child td:first-child::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 50%;
        width: 15px;
        border-top: 1px solid #dee2e6;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-briefcase"></i> Master Jabatan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-secondary" id="expandAll" title="Buka semua sub-kode">
                        <i class="fas fa-expand"></i> Buka Semua
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" id="collapseAll" title="Tutup semua sub-kode">
                        <i class="fas fa-compress"></i> Tutup Semua
                    </button>
                    <a href="{{ route('jabatan.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus"></i> Tambah Jabatan
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(count($groupedJabatan) > 0 && count($allJabatan) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="jabatanTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>KODE</th>
                                    <th>NAMA JABATAN</th>
                                    <th>UNOR</th>
                                    <th>JENIS</th>
                                    <th>JENJANG</th>
                                    <th>B</th>
                                    <th>K</th>
                                    <th>+/-</th>
                                    <th style="width: 140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $no = 1; @endphp
                                @foreach($groupedJabatan as $parentCode => $jabatanList)
                                    @php
                                        // Temukan item yang adalah parent dari group ini
                                        $parentJabatan = null;
                                        $childJabatan = [];
                                        
                                        foreach ($jabatanList as $item) {
                                            if ($parentCode === null || $item->kode === $parentCode) {
                                                $parentJabatan = $item;
                                            } else {
                                                $childJabatan[] = $item;
                                            }
                                        }
                                        
                                        // Jika parent tidak ditemukan di group ini, ambil item pertama sebagai parent
                                        if ($parentJabatan === null && count($jabatanList) > 0) {
                                            $parentJabatan = $jabatanList[0];
                                            $childJabatan = array_slice($jabatanList, 1);
                                        }
                                    @endphp

                                    @if($parentJabatan)
                                        <!-- Parent Row -->
                                        <tr class="jabatan-parent" data-parent-id="{{ $parentJabatan->id }}" data-parent-code="{{ $parentJabatan->kode }}">
                                            <td>
                                                @if(count($childJabatan) > 0)
                                                    <span class="toggle-icon">▶</span>
                                                @else
                                                    <span style="width: 16px; display: inline-block;"></span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-success">{{ $parentJabatan->kode }}</span>
                                            </td>
                                            <td><strong>{{ $parentJabatan->nama }}</strong></td>
                                            <td>{{ $parentJabatan->unitOrganisasi->nama ?? '-' }}</td>
                                            <td>{{ $parentJabatan->jenisJabatan->nama ?? '-' }}</td>
                                            <td>{{ $parentJabatan->jenjang->nama ?? '-' }}</td>
                                            <td><span class="badge bg-info">{{ $parentJabatan->b }}</span></td>
                                            <td><span class="badge bg-warning">{{ $parentJabatan->k }}</span></td>
                                            <td>
                                                @php
                                                    $gap = (int)$parentJabatan->b - (int)$parentJabatan->k;
                                                @endphp
                                                @if($gap > 0)
                                                    <span class="badge bg-danger">+{{ $gap }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $gap }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('jabatan.edit', $parentJabatan->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('jabatan.destroy', $parentJabatan->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="confirmDelete(event)" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @php $no++; @endphp

                                        <!-- Child Rows -->
                                        @foreach($childJabatan as $child)
                                            <tr class="jabatan-child" data-parent-id="{{ $parentJabatan->id }}">
                                                <td>{{ $no }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark">{{ $child->kode }}</span>
                                                </td>
                                                <td>{{ $child->nama }}</td>
                                                <td>{{ $child->unitOrganisasi->nama ?? '-' }}</td>
                                                <td>{{ $child->jenisJabatan->nama ?? '-' }}</td>
                                                <td>{{ $child->jenjang->nama ?? '-' }}</td>
                                                <td><span class="badge bg-info">{{ $child->b }}</span></td>
                                                <td><span class="badge bg-warning">{{ $child->k }}</span></td>
                                                <td>
                                                    @php
                                                        $gapChild = (int)$child->b - (int)$child->k;
                                                    @endphp
                                                    @if($gapChild > 0)
                                                        <span class="badge bg-danger">+{{ $gapChild }}</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $gapChild }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('jabatan.edit', $child->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('jabatan.destroy', $child->id) }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="confirmDelete(event)" title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @php $no++; @endphp
                                        @endforeach
                                    @endif
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const parentRows = document.querySelectorAll('.jabatan-parent');
    
    // Click handler untuk expand/collapse
    parentRows.forEach(row => {
        row.addEventListener('click', function() {
            const parentId = this.dataset.parentId;
            const childRows = document.querySelectorAll(`[data-parent-id="${parentId}"].jabatan-child`);
            
            // Hanya toggle jika ada child
            if (childRows.length > 0) {
                this.classList.toggle('collapsed');
                childRows.forEach(child => {
                    child.classList.toggle('show');
                });
            }
        });
    });
    
    // Expand All
    document.getElementById('expandAll').addEventListener('click', function() {
        parentRows.forEach(row => {
            const parentId = row.dataset.parentId;
            const childRows = document.querySelectorAll(`[data-parent-id="${parentId}"].jabatan-child`);
            
            if (childRows.length > 0) {
                row.classList.remove('collapsed');
                childRows.forEach(child => {
                    child.classList.add('show');
                });
            }
        });
    });
    
    // Collapse All
    document.getElementById('collapseAll').addEventListener('click', function() {
        parentRows.forEach(row => {
            const parentId = row.dataset.parentId;
            const childRows = document.querySelectorAll(`[data-parent-id="${parentId}"].jabatan-child`);
            
            if (childRows.length > 0) {
                row.classList.add('collapsed');
                childRows.forEach(child => {
                    child.classList.remove('show');
                });
            }
        });
    });
});
</script>
@endsection
