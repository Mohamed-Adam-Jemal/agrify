const PAGES = {
  dashboard:  { title: 'Dashboard',           subtitle: 'Live farm overview'           },
  irrigation: { title: 'Irrigation Control',  subtitle: 'Manage your irrigation zones' },
  stock:      { title: 'Stock Management',    subtitle: 'Track your inventory'          },
  production: { title: 'Production Tracking', subtitle: 'Planting and harvest log'     },
  alerts:     { title: 'Smart Alerts',        subtitle: 'Sensor threshold warnings'    },
  analytics:  { title: 'Analytics',           subtitle: 'Water usage and yield charts' },
  map:        { title: 'Farm Zone Map',        subtitle: 'Live zone layout and status'  },
};

const outlet  = document.getElementById('page-outlet');
const loaded  = {}; // simple cache — avoid re-fetching same page

async function navigate(page) {
  if (!PAGES[page]) page = 'dashboard';
  const meta = PAGES[page];

  // Nav highlight
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  document.querySelector(`[data-page="${page}"]`)?.classList.add('active');

  // Topbar
  document.getElementById('page-title').textContent    = meta.title;
  document.getElementById('page-subtitle').textContent = meta.subtitle;

  // Serve from cache or fetch
  if (loaded[page]) {
    outlet.innerHTML = loaded[page];
    return;
  }

  try {
    outlet.innerHTML = `<p style="padding:24px;color:#9ca3af;">Loading...</p>`;
    const res  = await fetch(`pages/${page}.html`);
    if (!res.ok) throw new Error(`${res.status}`);
    const html = await res.text();
    loaded[page]     = html;       // cache it
    outlet.innerHTML = html;
  } catch (err) {
    outlet.innerHTML = `<p style="padding:24px;color:red;">Failed to load page: pages/${page}.html</p>`;
  }
}

function tick() {
  const el = document.getElementById('clock');
  if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', e => {
      e.preventDefault();
      const page = link.dataset.page;
      history.pushState(null, '', `#${page}`);
      navigate(page);
    });
  });

  // Handle back/forward browser buttons
  window.addEventListener('popstate', () => {
    navigate(window.location.hash.replace('#', '') || 'dashboard');
  });

  // Load initial page from URL hash
  navigate(window.location.hash.replace('#', '') || 'dashboard');

  tick();
  setInterval(tick, 1000);
});