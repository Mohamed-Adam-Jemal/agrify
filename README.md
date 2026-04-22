# 🌿 Agrify — Smart Farm Management Platform
Agrify is a web platform that helps farmers monitor and manage their farm operations from a single interface. It covers field monitoring, irrigation control, stock management, and production tracking — with IoT devices simulated via JSON data.

---

## Features
- **Dashboard** — Live overview of farm sensors and key metrics
- **Irrigation Control** — Toggle irrigation zones remotely
- **Stock Management** — Track seeds, fertilizers, and equipment
- **Production Tracking** — Log planting cycles and harvest yields
- **Smart Alerts** — Warnings triggered by critical sensor thresholds
- **Analytics** — Water usage and production charts
- **Farm Zone Map** — Visual map to manage and monitor irrigation zones

---

## Tech Stack
- HTML5 / CSS3 / Vanilla JavaScript
- Chart.js for data visualization
- LocalStorage for data persistence
- JSON-based IoT device simulation

---

## Getting Started

```bash
git clone https://github.com/mohamed-adam-jemal/agrify.git
cd agrify
python3 -m http.server 3000
```

Then open your browser at `http://127.0.0.1:3000`

> ⚠️ **Important — Do not open `index.html` directly in the browser.**
> This project uses `fetch()` to load pages dynamically, which is blocked by browsers on `file://` URLs.
> You must serve it through a local HTTP server using the command above.

No other installation or build step required.

---

## Team

| | Member |
|---|---|
| Developer | [@mohamed-adam-jemal](https://github.com/mohamed-adam-jemal) |
| Developer | [@kiraaziz](https://github.com/kiraaziz) |

---

## License
MIT License
