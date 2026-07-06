import cytoscape from 'cytoscape';
import dagre from 'dagre';
import cytoscapeDagre from 'cytoscape-dagre';

cytoscape.use(cytoscapeDagre);

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
  el.style.color = isError ? '#b91c1c' : '#6b7280';
  el.innerText = msg;
}

async function renderGraph(container, pd = '') {
  container.innerHTML = '';
  showMessage('Memuat peta...');

  // jika pd kosong, batalkan (per requirement)
  if (!pd) {
    showMessage('Pilih Perangkat Daerah terlebih dahulu untuk memuat peta.');
    return;
  }

  let elements;
  try {
    elements = await fetchElements(pd);
  } catch (err) {
    console.error(err);
    showMessage(`Gagal memuat data dari server. Status: ${err.status || 'n/a'}. Periksa console/laravel.log`, true);
    const errBox = document.createElement('pre');
    errBox.style.background = '#fee2e2';
    errBox.style.padding = '12px';
    errBox.style.borderRadius = '6px';
    errBox.style.overflow = 'auto';
    errBox.textContent = err.body || err.message || 'Unknown error';
    container.appendChild(errBox);
    return;
  }

  if (!Array.isArray(elements) || elements.length === 0) {
    showMessage('Tidak ada data untuk perangkat daerah ini.');
    return;
  }

  try {
    destroyCy();

    cyInstance = cytoscape({
      container,
      elements,
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
            'width': '220px',
            'height': 'auto',
            'border-width': 0
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
            'curve-style': 'bezier',
            'opacity': 0.9
          }
        }
      ],
      layout: {
        name: 'dagre',
        rankDir: 'TB',
        nodeSep: 120,
        edgeSep: 10,
        rankSep: 120,
        align: 'DR'
      },
      minZoom: 0.1,
      maxZoom: 3,
      wheelSensitivity: 0.2
    });

    cyInstance.fit(50);
    showMessage(''); // clear message

    // klik node -> modal detail (jika jabatan)
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
          alert('Gagal memuat detail jabatan (cek console).');
        }
      }
    });

  } catch (e) {
    console.error(e);
    showMessage('Error saat merender graf. Periksa console.', true);
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('orgchart');
  const messageEl = document.getElementById('orgchart-message');
  const select = document.getElementById('pd-select');
  const btnLoad = document.getElementById('btn-load-organogram');

  function updateLoadState() {
    const pd = (select && select.value) ? select.value : '';
    btnLoad.disabled = !pd;
    if (!pd) {
      showMessage('Pilih Perangkat Daerah terlebih dahulu untuk memuat peta.');
      // clear container
      if (container) container.innerHTML = '';
      return false;
    }
    return true;
  }

  // initial state: do not auto-load if no PD selected
  updateLoadState();

  // load on button click
  btnLoad?.addEventListener('click', () => {
    const pd = select.value || '';
    if (!pd) return;
    renderGraph(container, pd);
  });

  // enable/disable load when selection changes
  select?.addEventListener('change', () => {
    updateLoadState();
  });

  // toolbar zoom & fit
  document.getElementById('zoom-in')?.addEventListener('click', () => {
    if (!cyInstance) return;
    cyInstance.zoom({ level: Math.min(cyInstance.zoom() * 1.2, cyInstance.maxZoom()), renderedPosition: { x: cyInstance.width()/2, y: cyInstance.height()/2 }});
  });
  document.getElementById('zoom-out')?.addEventListener('click', () => {
    if (!cyInstance) return;
    cyInstance.zoom({ level: Math.max(cyInstance.zoom() / 1.2, cyInstance.minZoom()), renderedPosition: { x: cyInstance.width()/2, y: cyInstance.height()/2 }});
  });
  document.getElementById('fit')?.addEventListener('click', () => {
    if (!cyInstance) return;
    cyInstance.fit(50);
  });
});