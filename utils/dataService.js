// Data service to handle all JSON data fetching
let dataCache = {};

export async function initDataService() {
  console.log('Initializing data service...');
  // Promise.allSettled means one failed fetch won't crash the whole dashboard
  const results = await Promise.allSettled([
    loadStockData(),
    loadProductionData(),
    loadAlertsData(),
    loadAnalyticsData(),
    loadZonesData(),
    loadFarmData(),      
    loadSensorsData()   
  ]);



  results.forEach((result, i) => {
    const names = ['stock', 'production', 'alerts', 'analytics', 'zones', 'farm', 'sensors'];
    if (result.status === 'rejected') {
      console.error(`❌ Failed to load ${names[i]}:`, result.reason);
    }
  });

  console.log('Data service initialized');
}

async function safeFetch(url) {
  const response = await fetch(url);
  if (!response.ok) {
    throw new Error(`Failed to fetch ${url}: HTTP ${response.status} ${response.statusText}`);
  }
  return response.json();
}

async function loadStockData() {
  if (!dataCache.stock) {
    dataCache.stock = await safeFetch('./data/stock.json');
  }
  return dataCache.stock;
}

async function loadProductionData() {
  if (!dataCache.production) {
    dataCache.production = await safeFetch('./data/production.json');
  }
  return dataCache.production;
}

async function loadAlertsData() {
  if (!dataCache.alerts) {
    dataCache.alerts = await safeFetch('./data/alerts.json');
  }
  return dataCache.alerts;
}

async function loadAnalyticsData() {
  if (!dataCache.analytics) {
    dataCache.analytics = await safeFetch('./data/analytics.json');
  }
  return dataCache.analytics;
}

async function loadZonesData() {
  if (!dataCache.zones) {
    dataCache.zones = await safeFetch('./data/zones.json');
  }
  return dataCache.zones;
}

async function loadFarmData() {
  if (!dataCache.farm) {
    dataCache.farm = await safeFetch('./data/farm.json');
  }
  return dataCache.farm;
}

async function loadSensorsData() {
  if (!dataCache.sensors) {
    dataCache.sensors = await safeFetch('./data/sensors.json');
  }
  return dataCache.sensors;
}

export async function getFarmData() {
  return await loadFarmData();
}

export async function getSensorsData() {
  return await loadSensorsData();
}

// Public API
export async function getStockData() {
  return await loadStockData();
}

export async function getProductionData() {
  return await loadProductionData();
}

export async function getActiveAlerts() {
  const alerts = await loadAlertsData();
  return alerts.filter(alert => alert.status === 'active');
}

export async function getAllAlerts() {
  return await loadAlertsData();
}

export async function getAnalyticsData() {
  return await loadAnalyticsData();
}

export async function getZonesData() {
  return await loadZonesData();
}

// Utility functions
export async function getLowStockItems(threshold = 10) {
  const stock = await loadStockData();
  return stock.filter(item => item.quantity < threshold);
}

export async function getCropProductionBySeason(season) {
  const production = await loadProductionData();
  return production.filter(crop => crop.season === season);
}

export async function getCriticalAlerts() {
  const alerts = await loadAlertsData();
  return alerts.filter(alert => alert.severity === 'critical' && alert.status === 'active');
}

// Refresh cache (useful for updates)
export function refreshCache() {
  dataCache = {};
  return initDataService();
}