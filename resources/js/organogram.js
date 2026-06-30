import cytoscape from 'cytoscape';
import cytoscapeDagre from 'cytoscape-dagre';
import dagre from 'dagre';

// Register plugin (cytoscape-dagre expects dagre present)
cytoscape.use(cytoscapeDagre);

document.addEventListener('DOMContentLoaded', async () => {
  const container = document.getElementById('orgchart');
  if (!container) return;

  // Ambil data graf dari backend
  let elements;
  try {
    const res = await fetch('/organogram/data');
    if (!res.ok) {
      container.innerText = 'Gagal memuat data peta jabatan';
      return;
    }
    elements = await res.json();
  } catch (err) {
    container.innerText = 'Gagal memuat data peta jabatan (network error)';
    console.error(err);
    return;
  }

  const cy = cytoscape({
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
      rankDir: 'TB', // top-to-bottom
      nodeSep: 50,
      edgeSep: 10,
      rankSep: 80
    },
    wheelSensitivity: 0.2
  });

  cy.fit();

  // Klik node => tampilkan info sederhana (ganti dengan modal/detail bila perlu)
  cy.on('tap', 'node', (evt) => {
    const node = evt.target;
    const label = node.data('label') || '—';
    // Anda bisa ganti alert dengan modal atau fetch detail endpoint
    alert(label);
  });
});