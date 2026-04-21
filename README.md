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
```

> ⚠️ **Important — Do not open `index.html` directly in the browser.**
> This project uses `fetch()` to load pages dynamically, which is blocked by browsers on `file://` URLs.
> You must serve it through a local HTTP server. We recommend using the **Live Server** extension in VS Code:
> 1. Install [Live Server](https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer) in VS Code
> 2. Right-click `index.html` → **Open with Live Server**
> 3. The app will open at `http://127.0.0.1:5500`

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