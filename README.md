# 🌿 Agrify
### Smart Farm Management Platform

> Agrify is a web-based platform that empowers farmers to monitor, manage, and control their farm operations from a single dashboard — including land management, stock inventory, production tracking, and IoT-simulated irrigation control.

---

## 🚀 Live Demo
> Coming soon

---

## 📸 Screenshots
> Coming soon

---

## 🧠 Features

- 📊 **Dashboard** — Real-time overview of all farm sensors and key metrics
- 💧 **Irrigation Control** — Toggle irrigation per zone via interactive controls
- 🗺️ **Farm Map** — Visual representation of farm fields and zones
- 📦 **Stock Management** — Track seeds, fertilizers, and equipment inventory
- 🌾 **Production Tracking** — Log planting cycles and harvest yields
- 🔔 **Smart Alerts** — Automatic warnings for critical sensor thresholds
- 📈 **Charts & Analytics** — Visualize water usage and production trends
- 💾 **Local Persistence** — Data saved via LocalStorage across sessions

---

## 🏗️ Architecture

```
agrify/
├── data/
│   ├── sensors.json        # Simulated IoT device data
│   ├── farm.json           # Land and field configuration
│   └── production.json     # Harvest and yield records
├── modules/
│   ├── dashboard.js        # Overview and live stats
│   ├── map.js              # Field visualization
│   ├── irrigation.js       # Zone control panel
│   ├── stock.js            # Inventory management
│   └── production.js       # Production tracking
├── components/
│   ├── chart.js            # Reusable chart wrapper (Chart.js)
│   ├── alert.js            # Notification system
│   └── modal.js            # Reusable modal component
├── styles/
│   ├── main.css            # Global styles
│   └── components.css      # Component-level styles
├── app.js                  # Router and state manager
└── index.html              # Entry point
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Markup | HTML5 |
| Styling | CSS3 |
| Logic | Vanilla JavaScript (ES6+) |
| Charts | Chart.js |
| Routing | Hash-based client-side router |
| Persistence | LocalStorage API |
| Device Simulation | JSON + setInterval engine |

> ⚠️ No frameworks. No build tools. Pure web standards.

---

## 📋 Agile Backlog

| # | User Story | Priority | Status |
|---|---|---|---|
| 1 | As a farmer, I want to see all my field sensors on a dashboard | 🔴 High | 🔲 Todo |
| 2 | As a farmer, I want to toggle irrigation per zone remotely | 🔴 High | 🔲 Todo |
| 3 | As a farmer, I want alerts when soil moisture is critically low | 🔴 High | 🔲 Todo |
| 4 | As a farmer, I want to track my stock of seeds and fertilizers | 🟡 Medium | 🔲 Todo |
| 5 | As a farmer, I want to log my harvest production over time | 🟡 Medium | 🔲 Todo |
| 6 | As a farmer, I want a visual map of my farm fields | 🟡 Medium | 🔲 Todo |
| 7 | As a farmer, I want my settings saved after page refresh | 🟠 Low | 🔲 Todo |
| 8 | As a farmer, I want charts showing my water usage trends | 🟠 Low | 🔲 Todo |

---

## 🗓️ Sprint Plan

**Sprint 1 — Foundation**
- [ ] Project structure setup
- [ ] Git repository and GitHub configuration
- [ ] index.html skeleton + hash router
- [ ] Simulated JSON sensor data

**Sprint 2 — Core Features**
- [ ] Dashboard with live sensor simulation
- [ ] Irrigation zone control panel

**Sprint 3 — Management Modules**
- [ ] Stock management module
- [ ] Production tracking module

**Sprint 4 — Polish & Delivery**
- [ ] Interactive farm map
- [ ] Alerts system
- [ ] Charts and analytics
- [ ] LocalStorage persistence
- [ ] Responsive design and final testing

---

## 👥 Team & Contribution

| Role | Member |
|---|---|
| 🧑‍💻 Lead Developer / Architecture | `@author1` |
| 🎨 UI/UX & Feature Developer | `@author2` |

### Branch Strategy
We follow the **Feature Branch Workflow**:
```bash
main                        # Always stable
├── feature/dashboard
├── feature/irrigation
├── feature/stock
└── feature/production
```

### Commit Convention
```
feat:     New feature
fix:      Bug fix
style:    UI/CSS changes
refactor: Code restructuring
docs:     Documentation updates
```

---

## ⚙️ Getting Started

```bash
# 1. Clone the repository
git clone https://github.com/your-username/agrify.git

# 2. Navigate into the project
cd agrify

# 3. Open in your browser
# Simply open index.html in any modern browser
# Or use VS Code Live Server extension for hot reload
```

No installation. No dependencies. No build step.

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

<p align="center">
  🌿 Built with passion for smarter farming · Agrify © 2025
</p>
