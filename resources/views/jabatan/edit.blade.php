@extends('layouts.app')

@section('title', 'Edit Jabatan')

@section('css')
<style>
  .form-group-hierarchical {
    margin-bottom: 20px;
  }

  .unit-level-label {
    display: block;
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .unit-option-group {
    margin-left: 12px;
    padding-left: 12px;
    border-left: 2px solid #e0e0e0;
  }

  .unit-option-group:nth-child(1) { border-left-color: #0b58a6; }
  .unit-option-group:nth-child(2) { border-left-color: #1976d2; }
  .unit-option-group:nth-child(3) { border-left-color: #42a5f5; }
  .unit-option-group:nth-child(4) { border-left-color: #90caf9; }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Jabatan</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('jabatan.update', $jabatan->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="kode" class="form-label">KODE <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode') is-invalid @enderror" id="kode" name="kode" value="{{ old('kode', $jabatan->kode) }}" required>
                        @error('kode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">NAMA JABATAN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $jabatan->nama) }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hierarchical Unit Selection -->
                    <div class="mb-3">
                        <label for="perangkat_daerah_id" class="form-label">PERANGKAT DAERAH <span class="text-danger">*</span></label>
                        <select class="form-select @error('perangkat_daerah_id') is-invalid @enderror" id="perangkat_daerah_id" required>
                            <option value="">-- Pilih Perangkat Daerah --</option>
                            @foreach($perangkatDaerah as $pd)
                                <option value="{{ $pd->id }}" @selected(old('perangkat_daerah_id', $jabatan->unitOrganisasi->perangkat_daerah_id ?? null) == $pd->id)>{{ $pd->nama }}</option>
                            @endforeach
                        </select>
                        @error('perangkat_daerah_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group-hierarchical">
                        <label for="unit_organisasi_id" class="form-label">UNIT ORGANISASI <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_organisasi_id') is-invalid @enderror" id="unit_organisasi_id" name="unit_organisasi_id" required>
                            <option value="{{ $jabatan->unit_organisasi_id }}">{{ $jabatan->unitOrganisasi->nama ?? 'Memuat...' }}</option>
                        </select>
                        @error('unit_organisasi_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenis_jabatan_id" class="form-label">JENIS JABATAN</label>
                        <select class="form-select @error('jenis_jabatan_id') is-invalid @enderror" id="jenis_jabatan_id" name="jenis_jabatan_id">
                            <option value="">-- Pilih Jenis Jabatan --</option>
                            @foreach($jenisJabatan as $item)
                                <option value="{{ $item->id }}" @selected(old('jenis_jabatan_id', $jabatan->jenis_jabatan_id) == $item->id)>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenis_jabatan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenjang_id" class="form-label">JENJANG</label>
                        <select class="form-select @error('jenjang_id') is-invalid @enderror" id="jenjang_id" name="jenjang_id">
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($jenjang as $item)
                                <option value="{{ $item->id }}" @selected(old('jenjang_id', $jabatan->jenjang_id) == $item->id)>{{ $item->nama }}</option>
                            @endforeach
                        </select>
                        @error('jenjang_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="kj" class="form-label">KJ (Kelas Jabatan)</label>
                        <input type="text" class="form-control @error('kj') is-invalid @enderror" id="kj" name="kj" value="{{ old('kj', $jabatan->kj) }}">
                        @error('kj')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="b" class="form-label">B (Bezetting) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('b') is-invalid @enderror" id="b" name="b" value="{{ old('b', $jabatan->b) }}" min="0" required>
                                @error('b')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="k" class="form-label">K (Kebutuhan) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('k') is-invalid @enderror" id="k" name="k" value="{{ old('k', $jabatan->k) }}" min="0" required>
                                @error('k')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('jabatan.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script>
const getUnitsHierarchyUrl = "{{ route('jabatan.unitsHierarchy', ':id') }}";
const currentUnitId = {{ $jabatan->unit_organisasi_id }};

document.getElementById('perangkat_daerah_id').addEventListener('change', async function() {
    const pdId = this.value;
    const unitSelect = document.getElementById('unit_organisasi_id');
    
    unitSelect.innerHTML = '<option value="">-- Memuat Unit Organisasi --</option>';
    
    if (!pdId) {
        unitSelect.innerHTML = '<option value="">-- Pilih Perangkat Daerah dulu --</option>';
        return;
    }

    try {
        const url = getUnitsHierarchyUrl.replace(':id', pdId);
        const response = await fetch(url, { credentials: 'same-origin' });
        const json = await response.json();

        if (json.success) {
            const data = json.data;
            let html = '<option value="">-- Pilih Unit Organisasi --</option>';

            // Render by level - TANPA DUPLIKAT
            Object.keys(data).sort((a, b) => parseInt(a) - parseInt(b)).forEach(level => {
                const units = data[level];
                
                units.forEach(unit => {
                    const indent = '— '.repeat(parseInt(level) - 1);
                    const selected = unit.id == currentUnitId ? 'selected' : '';
                    html += `<option value="${unit.id}" data-level="${level}" ${selected}>${indent}${unit.nama}</option>`;
                });
            });

            unitSelect.innerHTML = html;
        }
    } catch (error) {
        console.error('Error loading units:', error);
        unitSelect.innerHTML = '<option value="">Error memuat data</option>';
    }
});

// Trigger change event on page load to load units
window.addEventListener('load', function() {
    const pdSelect = document.getElementById('perangkat_daerah_id');
    if (pdSelect.value) {
        pdSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection