@extends('layouts.app')

@section('title', 'Peta Jabatan (Organogram)')

@section('content')
<div class="container-fluid p-4">
  <h1 class="text-3xl font-semibold mb-4">Peta Jabatan (Bagan Organisasi)</h1>

  <div class="mb-4 d-flex align-items-center gap-2">
    <label for="pd-select" class="font-medium">Pilih Perangkat Daerah:</label>
    <select id="pd-select" class="form-select" data-default-pd="{{ $perangkats->first()->id ?? '' }}" style="min-width: 420px;">
      <option value="">-- Semua Perangkat Daerah --</option>
      @foreach($perangkats as $pd)
        <option value="{{ $pd->id }}">{{ $pd->nama }}</option>
      @endforeach
    </select>

    <button type="button" class="btn btn-sm btn-primary" id="btn-load-organogram">Muat</button>

    <div class="ms-auto d-flex gap-2 align-items-center">
      <button id="zoom-in" class="btn btn-outline-secondary btn-sm">+</button>
      <button id="zoom-out" class="btn btn-outline-secondary btn-sm">–</button>
      <button id="fit" class="btn btn-outline-secondary btn-sm">Fit</button>
    </div>
  </div>

  <div id="orgchart-panel" class="card p-3">
    <div id="orgchart" style="width:100%; height:72vh; background:#ffffff; border-radius:6px; border:1px solid #eef2f6;"></div>
    <div id="orgchart-message" style="margin-top:12px; color:#6b7280;">
      Pilih Perangkat Daerah yang ingin ditampilkan lalu klik "Muat".
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
  <script>
    window.ORG_URLS = {
      data: "{{ route('organogram.data') }}",
      detailPrefix: "{{ url('organogram/detail') }}"
    };
  </script>

  <!-- Load dari CDN - PENTING: dagre harus sebelum cytoscape-dagre -->
  <script src="https://unpkg.com/dagre@0.8.5/dist/dagre.js"></script>
  <script src="https://unpkg.com/cytoscape@3.28.1/dist/cytoscape.umd.js"></script>
  <script src="https://unpkg.com/cytoscape-dagre@2.5.0/dist/cytoscape-dagre.umd.js"></script>

  <script>
    // Register dagre plugin SETELAH semua library dimuat
    if (typeof cytoscape !== 'undefined' && typeof cytoscapeDagre !== 'undefined') {
      cytoscape.use(cytoscapeDagre);
      console.log('[OK] Cytoscape + Dagre plugin berhasil dimuat');
    } else {
      console.error('[ERROR] Cytoscape atau Dagre tidak berhasil dimuat');
    }

    let cyInstance = null;

    function getDataUrl(pd = '') {
      if (window.ORG_URLS && window.ORG_URLS.data) {
        return window.ORG_URLS.data + (pd ? `?pd=${encodeURIComponent(pd)}` : '');
      }
      return '/organogram/data' + (pd ? `?pd=${encodeURIComponent(pd)}` : '');
    }

    function getDetailUrl(id) {
      if (window.ORG_URLS && window.ORG_URLS.detailPrefix) {
        return window.ORG_URLS.detailPrefix.replace(/\/$/, '') + '/' + encodeURIComponent(id);
      }
      return `/organogram/detail/${encodeURIComponent(id)}`;
    }

    async function fetchElements(pd = '') {
      const url = getDataUrl(pd);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      if (!res.ok) {
        const text = await res.text();
        const err = new Error(`Fetch failed: ${res.status} ${res.statusText}`);
        err.status = res.status;
        err.body = text;
        throw err;
      }
      return await res.json();
    }

    async function fetchJabatanDetail(jid) {
      const url = getDetailUrl(jid);
      const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
      if (!res.ok) {
        const text = await res.text();
        throw new Error(`Detail fetch failed ${res.status}: ${text}`);
      }
      return await res.json();
    }

    function destroyCy() {
      if (cyInstance) {
        try { cyInstance.destroy(); } catch(e) {}
        cyInstance = null;
      }
    }

    function showMessage(msg, isError = false) {
      const el = document.getElementById('orgchart-message');
      if (el) {
        el.style.color = isError ? '#dc2626' : '#4b5563';
        el.innerText = msg;
      }
    }

    async function renderGraph(container, pd = '') {
      if (!container) return;

      container.innerHTML = '';
      showMessage('Memuat peta...');

      if (!pd) {
        showMessage('Pilih Perangkat Daerah terlebih dahulu untuk memuat peta.');
        return;
      }

      let elements;
      try {
        elements = await fetchElements(pd);
      } catch (err) {
        console.error(err);
        showMessage(`Gagal memuat data: ${err.message}`, true);
        return;
      }

      if (!Array.isArray(elements) || elements.length === 0) {
        showMessage('Tidak ada data untuk perangkat daerah ini.');
        return;
      }

      try {
        if (typeof cytoscape === 'undefined') {
          showMessage('Cytoscape library tidak berhasil dimuat', true);
          return;
        }

        destroyCy();

        cyInstance = cytoscape({
          container: container,
          elements: elements,
          style: [
            {
              selector: 'node[type="perangkat_daerah"]',
              style: {
                'shape': 'roundrectangle',
                'background-color': '#0f172a',
                'label': 'data(label)',
                'color': '#ffffff',
                'text-valign': 'center',
                'text-halign': 'center',
                'font-size': '14px',
                'text-wrap': 'wrap',
                'text-max-width': '220px',
                'padding': '12px',
                'width': '220px'
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
                'font-size': '12px',
                'text-wrap': 'wrap',
                'text-max-width': '180px',
                'padding': '8px',
                'width': '180px'
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
                'font-size': '11px',
                'text-wrap': 'wrap',
                'text-max-width': '160px',
                'padding': '6px',
                'width': '160px'
              }
            },
            {
              selector: 'edge',
              style: {
                'width': 2,
                'line-color': '#cbd5e1',
                'target-arrow-shape': 'triangle',
                'target-arrow-color': '#cbd5e1',
                'curve-style': 'bezier'
              }
            }
          ],
          layout: {
            name: 'dagre',
            rankDir: 'TB',
            nodeSep: 120,
            edgeSep: 10,
            rankSep: 120
          },
          wheelSensitivity: 0.2
        });

        cyInstance.fit(50);
        showMessage('');

        cyInstance.on('tap', 'node', async (evt) => {
          const node = evt.target;
          const type = node.data('type');
          if (type === 'jabatan') {
            const realId = node.data('realId');
            try {
              const detail = await fetchJabatanDetail(realId);
              document.getElementById('jab-nama').innerText = detail.nama || '-';
              document.getElementById('jab-kode').innerText = detail.kode || '-';
              document.getElementById('jab-pd').innerText = detail.perangkat_daerah || '-';
              document.getElementById('jab-unit').innerText = detail.unit || '-';
              document.getElementById('jab-k').innerText = detail.kebutuhan ?? 0;
              document.getElementById('jab-b').innerText = detail.bezetting ?? 0;
              document.getElementById('jab-gap').innerText = detail.gap ?? 0;
              const modalEl = document.getElementById('orgchartModal');
              const modal = new bootstrap.Modal(modalEl);
              modal.show();
            } catch (e) {
              console.error(e);
              alert('Gagal memuat detail jabatan');
            }
          }
        });

      } catch (e) {
        console.error(e);
        showMessage('Error saat merender graf: ' + e.message, true);
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      const container = document.getElementById('orgchart');
      const select = document.getElementById('pd-select');
      const btnLoad = document.getElementById('btn-load-organogram');

      if (!container) return;

      function updateLoadState() {
        const pd = (select && select.value) ? select.value : '';
        if (btnLoad) btnLoad.disabled = !pd;
        if (!pd) {
          showMessage('Pilih Perangkat Daerah terlebih dahulu untuk memuat peta.');
          container.innerHTML = '';
          return false;
        }
        return true;
      }

      updateLoadState();

      if (btnLoad) {
        btnLoad.addEventListener('click', () => {
          const pd = select.value || '';
          if (!pd) return;
          renderGraph(container, pd);
        });
      }

      if (select) {
        select.addEventListener('change', () => {
          updateLoadState();
        });
      }

      document.getElementById('zoom-in')?.addEventListener('click', () => {
        if (!cyInstance) return;
        const z = cyInstance.zoom();
        cyInstance.zoom({ level: Math.min(z * 1.2, 3) });
      });

      document.getElementById('zoom-out')?.addEventListener('click', () => {
        if (!cyInstance) return;
        const z = cyInstance.zoom();
        cyInstance.zoom({ level: Math.max(z / 1.2, 0.1) });
      });

      document.getElementById('fit')?.addEventListener('click', () => {
        if (!cyInstance) return;
        cyInstance.fit(50);
      });
    });
  </script>
@endpush