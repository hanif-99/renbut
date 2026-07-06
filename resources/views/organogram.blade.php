@extends('layouts.app')

@section('title', 'Peta Jabatan')

@section('content')
<div class="container-fluid p-0">
  <div class="mb-4 d-flex align-items-center gap-2">
    <select id="pd-select" class="form-select" style="min-width: 420px;">
      <option value="">-- Pilih Perangkat Daerah --</option>
      @foreach($perangkats as $pd)
        <option value="{{ $pd->id }}">{{ $pd->nama }}</option>
      @endforeach
    </select>

    <button type="button" class="btn btn-sm btn-primary" id="btn-load-organogram">Proses</button>

    <div class="ms-auto d-flex gap-2 align-items-center">
      <button id="zoom-in" class="btn btn-outline-secondary btn-sm">+</button>
      <button id="zoom-out" class="btn btn-outline-secondary btn-sm">–</button>
      <button id="fit" class="btn btn-outline-secondary btn-sm">Fit</button>
    </div>
  </div>

  <div id="orgchart-panel" class="card p-3">
    <div id="orgchart" style="width:100%; height:72vh; background:#f9fafb; border-radius:6px; border:1px solid #eef2f6;"></div>
    <div id="orgchart-message" style="margin-top:12px; color:#6b7280; font-size:14px;">
      Pilih Perangkat Daerah yang ingin ditampilkan lalu klik "Proses".
    </div>
  </div>
</div>

<!-- Modal detail jabatan -->
<div class="modal fade" id="orgchartModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Jabatan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><strong>Nama:</strong> <span id="jab-nama">-</span></p>
        <p><strong>Kode:</strong> <span id="jab-kode">-</span></p>
        <p><strong>Perangkat Daerah:</strong> <span id="jab-pd">-</span></p>
        <p><strong>Unit Organisasi:</strong> <span id="jab-unit">-</span></p>
        <p><strong>Kebutuhan (K):</strong> <span id="jab-k">0</span></p>
        <p><strong>Bezetting (B):</strong> <span id="jab-b">0</span></p>
        <p><strong>Gap (B - K):</strong> <span id="jab-gap">0</span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <!-- Hanya load Cytoscape dari jsDelivr (tanpa plugin yang bermasalah) -->
  <script src="https://cdn.jsdelivr.net/npm/cytoscape@3.28.1/dist/cytoscape.umd.js"></script>

  <script>
    console.log('[ORG] Script initialized');

    let cyInstance = null;
    const orgDataUrl = "{{ route('organogram.data') }}";
    const orgDetailUrl = "{{ url('organogram/detail') }}";

    function showMsg(text, isError = false) {
      const el = document.getElementById('orgchart-message');
      if (el) {
        el.textContent = text;
        el.style.color = isError ? '#dc2626' : '#6b7280';
      }
    }

    function loadChart() {
      const pdSelect = document.getElementById('pd-select');
      const pdId = pdSelect ? pdSelect.value : '';
      
      if (!pdId) {
        showMsg('Pilih Perangkat Daerah terlebih dahulu', false);
        return;
      }

      const container = document.getElementById('orgchart');
      if (!container) {
        console.error('Container not found');
        return;
      }

      container.innerHTML = '';
      showMsg('Memuat data...');

      const fetchUrl = orgDataUrl + '?pd=' + encodeURIComponent(pdId);

      fetch(fetchUrl)
        .then(res => {
          if (!res.ok) throw new Error('HTTP ' + res.status);
          return res.json();
        })
        .then(elements => {
          console.log('Elements received:', elements.length);
          
          if (!Array.isArray(elements) || elements.length === 0) {
            showMsg('Tidak ada data untuk perangkat daerah ini', false);
            return;
          }

          // Destroy previous instance
          if (cyInstance) {
            cyInstance.destroy();
            cyInstance = null;
          }

          // Create Cytoscape dengan layout breadthfirst (built-in, tidak perlu plugin)
          cyInstance = window.cytoscape({
            container: container,
            elements: elements,
            style: [
              {
                selector: 'node[type="perangkat_daerah"]',
                style: {
                  'shape': 'roundrectangle',
                  'background-color': '#0f172a',
                  'label': 'data(label)',
                  'color': '#fff',
                  'text-valign': 'center',
                  'text-halign': 'center',
                  'font-size': '13px',
                  'text-wrap': 'wrap',
                  'text-max-width': '200px',
                  'padding': '12px',
                  'width': '200px',
                  'height': '60px',
                  'border-width': '2px',
                  'border-color': '#1e3a8a'
                }
              },
              {
                selector: 'node[type="unit_organisasi"]',
                style: {
                  'shape': 'roundrectangle',
                  'background-color': '#1e40af',
                  'label': 'data(label)',
                  'color': '#fff',
                  'text-valign': 'center',
                  'text-halign': 'center',
                  'font-size': '11px',
                  'text-wrap': 'wrap',
                  'text-max-width': '160px',
                  'padding': '8px',
                  'width': '160px',
                  'height': '50px'
                }
              },
              {
                selector: 'node[type="jabatan"]',
                style: {
                  'shape': 'roundrectangle',
                  'background-color': '#047857',
                  'label': 'data(label)',
                  'color': '#fff',
                  'text-valign': 'center',
                  'text-halign': 'center',
                  'font-size': '10px',
                  'text-wrap': 'wrap',
                  'text-max-width': '140px',
                  'padding': '6px',
                  'width': '140px',
                  'height': '45px',
                  'cursor': 'pointer'
                }
              },
              {
                selector: 'node:hover',
                style: {
                  'opacity': 0.8,
                  'box-shadow': '0 0 10px rgba(0,0,0,0.3)'
                }
              },
              {
                selector: 'edge',
                style: {
                  'width': 2,
                  'line-color': '#cbd5e1',
                  'target-arrow-shape': 'triangle',
                  'target-arrow-color': '#cbd5e1',
                  'curve-style': 'bezier',
                  'target-arrow-fill': 'filled'
                }
              }
            ],
            // Gunakan layout breadthfirst (built-in, tidak perlu plugin)
            layout: {
              name: 'breadthfirst',
              directed: true,
              roots: '[type="perangkat_daerah"]',
              padding: 50,
              spacingFactor: 1.5
            },
            wheelSensitivity: 0.2,
            maxZoom: 3,
            minZoom: 0.1
          });

          cyInstance.fit(30);
          showMsg('Peta Jabatan berhasil dimuat');
          console.log('Cytoscape instance created and displayed');

          // Click handler untuk jabatan nodes
          cyInstance.on('tap', 'node[type="jabatan"]', function(evt) {
            const realId = evt.target.data('realId');
            
            fetch(orgDetailUrl + '/' + realId)
              .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
              })
              .then(detail => {
                document.getElementById('jab-nama').textContent = detail.nama || '-';
                document.getElementById('jab-kode').textContent = detail.kode || '-';
                document.getElementById('jab-pd').textContent = detail.perangkat_daerah || '-';
                document.getElementById('jab-unit').textContent = detail.unit || '-';
                document.getElementById('jab-k').textContent = detail.kebutuhan || 0;
                document.getElementById('jab-b').textContent = detail.bezetting || 0;
                document.getElementById('jab-gap').textContent = detail.gap || 0;
                new bootstrap.Modal(document.getElementById('orgchartModal')).show();
              })
              .catch(e => {
                console.error('Detail error:', e);
                alert('Gagal memuat detail jabatan');
              });
          });
        })
        .catch(err => {
          console.error('Error:', err);
          showMsg('Error: ' + err.message, true);
        });
    }

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      const btn = document.getElementById('btn-load-organogram');
      if (btn) {
        btn.addEventListener('click', loadChart);
      }

      // Zoom controls
      document.getElementById('zoom-in')?.addEventListener('click', function() {
        if (cyInstance) {
          const z = cyInstance.zoom();
          cyInstance.zoom({ level: Math.min(z * 1.2, 3) });
        }
      });

      document.getElementById('zoom-out')?.addEventListener('click', function() {
        if (cyInstance) {
          const z = cyInstance.zoom();
          cyInstance.zoom({ level: Math.max(z / 1.2, 0.1) });
        }
      });

      document.getElementById('fit')?.addEventListener('click', function() {
        if (cyInstance) {
          cyInstance.fit(30);
        }
      });
    });
  </script>
@endpush