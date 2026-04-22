// app.js
import { 
  initDataService, 
  getActiveAlerts, 
  getAllAlerts,
  getStockData,
  getProductionData,
  getAnalyticsData,
  getZonesData,
  getFarmData,    
  getSensorsData
} from './utils/dataService.js';

const PAGES = {
  dashboard:  { title: 'Dashboard',      subtitle: 'Live farm overview', needsData: true },
  stock:      { title: 'Stock',          subtitle: 'Track your inventory', needsData: true },
  production: { title: 'Production',     subtitle: 'Planting and harvest log', needsData: true },
  alerts:     { title: 'Alerts',         subtitle: 'Sensor threshold warnings', needsData: true },
  analytics:  { title: 'Analytics',      subtitle: 'Water usage and yield', needsData: true },
  map:        { title: 'Farm Zone Map',  subtitle: 'Manage irrigation zones', needsData: false },
};

const outlet = document.getElementById('page-outlet');

// Re-execute <script> tags after injecting HTML into the DOM.
// innerHTML does NOT run scripts — this clones and re-inserts them so they execute.
function reinitScripts(container) {
  container.querySelectorAll('script').forEach(oldScript => {
    const newScript = document.createElement('script');
    [...oldScript.attributes].forEach(attr =>
      newScript.setAttribute(attr.name, attr.value)
    );
    newScript.textContent = oldScript.textContent;
    oldScript.parentNode.replaceChild(newScript, oldScript);
  });
}

async function navigate(page) {
  console.log('Navigating to:', page);
  
  if (!PAGES[page]) page = 'dashboard';
  const meta = PAGES[page];

  if (meta.needsData) {
    try {
      await initDataService();
      console.log('✅ Data service ready for:', page);
    } catch (err) {
      console.error('Data service init failed:', err);
    }
  }

  // Update nav highlight
  document.querySelectorAll('.nav-item').forEach(el => el.classList.remove('active'));
  const activeNav = document.querySelector(`[data-page="${page}"]`);
  if (activeNav) activeNav.classList.add('active');

  // Update topbar
  document.getElementById('page-title').textContent = meta.title;
  document.getElementById('page-subtitle').textContent = meta.subtitle;

  await updateAlertBadge();

  // Always fetch fresh from disk — no caching
  try {
    outlet.innerHTML = `<div class="loading-spinner">Loading ${meta.title}...</div>`;
    const res = await fetch(`./pages/${page}.html`);
    if (!res.ok) throw new Error(`HTTP ${res.status} - ${res.statusText}`);
    const html = await res.text();

    outlet.innerHTML = html;
    reinitScripts(outlet);
    await initializePageData(page);
  } catch (err) {
    console.error('Navigation error:', err);
    outlet.innerHTML = `
      <div class="error-message">
        <h3>❌ Failed to load ${meta.title}</h3>
        <p>Error: ${err.message}</p>
        <p>Make sure <strong>pages/${page}.html</strong> exists</p>
        <button onclick="location.reload()">Refresh Page</button>
      </div>
    `;
  }
}

async function initializePageData(page) {
  console.log('Initializing data for:', page);
  
  try {
    if (page === 'dashboard') {
      const [stock, production, alerts, analytics, farm, sensors] = await Promise.all([
        getStockData(),
        getProductionData(),
        getActiveAlerts(),
        getAnalyticsData(),
        getFarmData(),    
        getSensorsData()  
      ]);
      const data = { stock, production, alerts, analytics, farm, sensors };
      window._dashboardData = data;
      window.dispatchEvent(new CustomEvent('dashboardDataLoaded', { detail: data }));
    }

    else if (page === 'alerts') {
      const alerts = await getAllAlerts();
      window.dispatchEvent(new CustomEvent('alertsDataLoaded', { detail: alerts }));
    }

    else if (page === 'stock') {
      const stock = await getStockData();
      window.dispatchEvent(new CustomEvent('stockDataLoaded', { detail: stock }));
    }

    else if (page === 'production') {
      const production = await getProductionData();
      window.dispatchEvent(new CustomEvent('productionDataLoaded', { detail: production }));
    }

    else if (page === 'analytics') {
      const analytics = await getAnalyticsData();
      window.dispatchEvent(new CustomEvent('analyticsDataLoaded', { detail: analytics }));
    }

    else if (page === 'map') {
      const zones = await getZonesData();
      window.dispatchEvent(new CustomEvent('mapDataLoaded', { detail: zones }));
    }

  } catch (err) {
    console.error(`Failed to load data for ${page}:`, err);
  }
}

async function updateAlertBadge() {
  const badge = document.getElementById('alert-badge');
  if (!badge) return;
  try {
    const alerts = await getActiveAlerts();
    const count = alerts.length;
    badge.textContent = count;
    badge.style.display = count > 0 ? 'inline-block' : 'none';
  } catch (err) {
    console.error('Failed to update alert badge:', err);
  }
}

function tick() {
  const el = document.getElementById('clock');
  if (el) {
    el.textContent = new Date().toLocaleTimeString('en-US', { 
      hour: '2-digit', 
      minute: '2-digit' 
    });
  }
}

document.addEventListener('DOMContentLoaded', () => {
  console.log('App started - Python server mode');
  console.log('Current path:', window.location.pathname);
  
  document.querySelectorAll('.nav-item').forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const page = link.dataset.page;
      if (page) {
        history.pushState(null, '', `#${page}`);
        navigate(page);
      }
    });
  });

  window.addEventListener('popstate', () => {
    const hash = window.location.hash.replace('#', '');
    navigate(hash || 'dashboard');
  });

  const initialPage = window.location.hash.replace('#', '') || 'dashboard';
  navigate(initialPage);
  
  tick();
  setInterval(tick, 1000);
  setInterval(updateAlertBadge, 30000);
});