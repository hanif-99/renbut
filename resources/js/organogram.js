import cytoscape from 'cytoscape';
import cytoscapeDagre from 'cytoscape-dagre';
import dagre from 'dagre';

// register plugin
cytoscape.use(cytoscapeDagre);

let cyInstance = null;

async function fetchElements(pd = '') {
  const url = '/organogram/data' + (pd ? `?pd=${encodeURIComponent(pd)}` : '');
  const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
  if (!res.ok) throw new Error('Failed to fetch organogram data');
  return await res.json();
}

async function fetchJabatanDetail(jid) {
  const url = `/organogram/detail/${encodeURIComponent(jid)}`;
  const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
  if (!res.ok) throw new Error('Failed to fetch jabatan detail');
  return await res.json();
}

function destroyCy() {
  if (cyInstance) {
    try {
      cyInstance.destroy();
    } catch (e) {}
    cyInstance = null;
  }
}

async function renderGraph(container, pd = '') {
  container.innerHTML = ''; // clear
  let elements;
  try {
    elements = await fetchElements(pd);
  } catch (err) {
    container.innerText = 'Gagal memuat data peta jabatan';
    console.error(err);
    return;
  }

  destroyCy();

  cyInstance = cytoscape({
    container,
    elements,
    style: [
      {
        selector: 'node[type="perangkat_daerah"]',
        style: {
          'background-color': '#111827',
          'label': 'data(label)',
          'color': '#fff',
          'text-valign': 'center',
          'text-halign': 'center',
          'font-size': '14px',
          'text-wrap': 'wrap',
          'width': 'label',
          'padding': '10px'
        }
      },
      {
        selector: 'node[type="unit_organisasi"]',
        style: {
          'background-color': '#2563eb',
          'label': 'data(label)',
          'color': '#fff',
          'text-valign': 'center',
          'text-halign': 'center',
          'font-size': '12px',
          'text-wrap': 'wrap',
          'width': 'label',
          'padding': '8px'
        }
      },
      {
        selector: 'node[type="jabatan"]',
        style: {
          'background-color': '#10b981',
          'label': 'data(label)',
          'color': '#fff',
          'text-valign': 'center',
          'text-halign': 'center',
          'font-size': '11px',
          'text-wrap': 'wrap',
          'width': 'label',
          'padding': '6px'
        }
      },
      {
        selector: 'edge',
        style: {
          'width': 2,
          'line-color': '#d1d5db',
          'target-arrow-shape': 'triangle',
          'target-arrow-color': '#d1d5db',
          'curve-style': 'bezier'
        }
      }
    ],
    layout: {
      name: 'dagre',
      rankDir: 'TB',
      nodeSep: 50,
      edgeSep: 10,
      rankSep: 80
    },
    wheelSensitivity: 0.2
  });

  cyInstance.fit();

  // Klik node -> jika jabatan, fetch detail dan tunjukkan modal
  cyInstance.on('tap', 'node', async (evt) => {
    const node = evt.target;
    const type = node.data('type');
    if (type === 'jabatan') {
      const realId = node.data('realId');
      try {
        const detail = await fetchJabatanDetail(realId);
        // isi modal
        document.getElementById('jab-nama').innerText = detail.nama || '-';
        document.getElementById('jab-kode').innerText = detail.kode || '-';
        document.getElementById('jab-pd').innerText = detail.perangkat_daerah || '-';
        document.getElementById('jab-unit').innerText = detail.unit || '-';
        document.getElementById('jab-k').innerText = detail.kebutuhan ?? 0;
        document.getElementById('jab-b').innerText = detail.bezetting ?? 0;
        document.getElementById('jab-gap').innerText = detail.gap ?? 0;

        // tampilkan modal (Bootstrap 5)
        const modalEl = document.getElementById('orgchartModal');
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      } catch (err) {
        console.error(err);
        alert('Gagal memuat detail jabatan');
      }
    }
  });

  // responsif
  window.addEventListener('resize', () => {
    if (cyInstance) {
      cyInstance.resize();
      cyInstance.fit();
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('orgchart');
  if (!container) return;

  const select = document.getElementById('pd-select');
  const defaultPd = select?.dataset?.defaultPd || '';
  if (defaultPd) {
    select.value = defaultPd;
    renderGraph(container, defaultPd);
  } else {
    // load all
    renderGraph(container, '');
  }

  const btnLoad = document.getElementById('btn-load-organogram');
  btnLoad?.addEventListener('click', () => {
    const pd = select.value || '';
    renderGraph(container, pd);
  });

  // load on change (optional)
  select?.addEventListener('change', () => {
    const pd = select.value || '';
    // debounce simple
    if (window.__org_reload_timeout) clearTimeout(window.__org_reload_timeout);
    window.__org_reload_timeout = setTimeout(() => {
      renderGraph(container, pd);
    }, 300);
  });
});