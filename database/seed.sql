-- ============================================================
--  agrify | seed.sql
--  Dev-only sample data. Run AFTER schema.sql.
--  mysql -u root -p < database/seed.sql
--  Covers: admins, users, farmers, farms, zones,
--          workers, production, stock
-- ============================================================

USE `agrify`;

-- ============================================================
--  Admin
-- ============================================================
-- Password: admin1234
-- Regenerate: php -r "echo password_hash('admin1234', PASSWORD_BCRYPT);"
INSERT IGNORE INTO `admins` (`username`, `email`, `password`, `is_active`)
VALUES (
  'admin',
  'admin@agrify.local',
  '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- admin1234
  1
);

-- ============================================================
--  Users (2 farmers + 2 workers per farmer = 6 users total)
-- ============================================================
-- Passwords are all: password1234
INSERT IGNORE INTO `users` (`id`, `username`, `email`, `password`, `is_active`)
VALUES
  -- Farmers
  (1, 'youssef_farmer', 'youssef@agrify.local', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
  (2, 'tariq_farmer',   'tariq@agrify.local',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
  -- Youssef's workers
  (3, 'omar_worker',    'omar@agrify.local',    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
  (4, 'bilal_worker',   'bilal@agrify.local',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
  -- Tariq's workers
  (5, 'hamza_worker',   'hamza@agrify.local',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
  (6, 'nabil_worker',   'nabil@agrify.local',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- ============================================================
--  Farmers
-- ============================================================
INSERT IGNORE INTO `farmers` (`id`, `user_id`, `first_name`, `last_name`, `phone`)
VALUES
  (1, 1, 'Youssef', 'Mansouri', '+216-55-010-101'),
  (2, 2, 'Tariq',   'Belhaj',   '+216-55-020-202');

-- ============================================================
--  Farms (1 per farmer)
-- ============================================================
INSERT IGNORE INTO `farms`
  (`id`, `farmer_id`, `name`, `location`, `total_area`, `area_unit`,
   `irrigation_type`, `owner_name`, `contact_email`, `contact_phone`,
   `latitude`, `longitude`, `is_active`)
VALUES
  (1, 1,
   'Mazraat Al-Mansouri', 'Sidi Bou Ali, Sfax, Tunisia',
   130.00, 'hectares', 'Drip System',
   'Youssef Al-Mansouri', 'youssef@agrify.local', '+216-55-010-101',
   35.0627, 10.6901, 1),

  (2, 2,
   'Dhaït Belhaj', 'Al-Ain, Sfax, Tunisia',
   90.00, 'hectares', 'Sprinkler',
   'Tariq Belhaj', 'tariq@agrify.local', '+216-55-020-202',
   35.0810, 10.7050, 1);

-- ============================================================
--  Zones (3 per farm)
-- ============================================================
INSERT IGNORE INTO `zones`
  (`id`, `farm_id`, `name`, `area`, `crop`, `latitude`, `longitude`, `is_active`)
VALUES
  -- Mazraat Al-Mansouri zones (coordinates from zones.json)
  ('zone_GV_A', 1, 'Al-Haql Al-Shamali',   45.00, 'Wheat',     35.0627074, 10.6900841, 1),
  ('zone_GV_B', 1, 'Al-Wadi Al-Sharqi',    35.00, 'Corn',      35.0627074, 10.6960841, 1),
  ('zone_GV_C', 1, 'Al-Marj Al-Janubi',    40.00, 'Soybeans',  35.0570747, 10.6900841, 1),
  ('zone_GV_D', 1, 'Al-Hadiqa Al-Gharbiya',10.00, 'Tomatoes',  35.0583074, 10.6956941, 1),

  ('zone_BH_A', 2, 'Qitat Al-Zaytoun',     35.00, 'Olives',    35.0820000, 10.7060000, 1),
  ('zone_BH_B', 2, 'Haql Al-Hubub',        30.00, 'Barley',    35.0800000, 10.7040000, 1),
  ('zone_BH_C', 2, 'Bustan Al-Tamr',       25.00, 'Dates',     35.0790000, 10.7070000, 1);

-- ============================================================
--  Workers (2 per farm)
-- ============================================================
INSERT IGNORE INTO `workers`
  (`id`, `user_id`, `farm_id`, `first_name`, `last_name`,
   `phone`, `job_title`, `salary`, `salary_currency`)
VALUES
  -- Mazraat Al-Mansouri workers
  (1, 3, 1, 'Omar',  'Chouikhi', '+216-55-030-101', 'Field Operator',        2800.00, 'TND'),
  (2, 4, 1, 'Bilal', 'Rezgui',   '+216-55-030-202', 'Irrigation Technician', 3200.00, 'TND'),
  -- Dii'at Belhaj workers
  (3, 5, 2, 'Hamza', 'Trabelsi', '+216-55-040-101', 'Field Operator',        2800.00, 'TND'),
  (4, 6, 2, 'Nabil', 'Gharbi',   '+216-55-040-202', 'Harvest Supervisor',    3500.00, 'TND');

-- ============================================================
--  Production (3 records per farm, different statuses)
-- ============================================================
INSERT IGNORE INTO `production`
  (`farm_id`, `zone_id`, `crop`, `area`, `area_unit`, `season`,
   `planting_date`, `expected_harvest`, `actual_harvest`,
   `status`, `yield`, `yield_unit`, `notes`)
VALUES
 
  (1, 'zone_GV_A', 'Wheat',    45.00, 'hectares', 'winter',
   '2024-11-01', '2025-04-15', '2025-04-20',
   'harvested', 135.00, 'tons', 'Mahsoul jayyid raghm al-bard al-mutaakhir.'),

  (1, 'zone_GV_B', 'Corn',     35.00, 'hectares', 'spring',
   '2025-02-01', '2025-08-01', NULL,
   'growing', NULL, 'tons', 'Al-ri bil-taqtir nasht.'),

  (1, 'zone_GV_C', 'Soybeans', 40.00, 'hectares', 'summer',
   '2025-05-01', '2025-09-01', NULL,
   'planned', NULL, 'tons', 'Jari tahdeer al-turba.'),

  (1, 'zone_GV_D', 'Tomatoes', 10.00, 'hectares', 'spring',
   '2025-03-15', '2025-07-15', NULL,
   'growing', NULL, 'tons', 'Al-numuw muntazam, la mashakil tudhkar.'),

 
  (2, 'zone_BH_A', 'Olives',  35.00, 'hectares', 'autumn',
   '2024-09-01', '2025-11-01', NULL,
   'growing', NULL, 'tons', 'Ashjar zaytun muammara, intaj mutawaqqa jayyid.'),

  (2, 'zone_BH_B', 'Barley',  30.00, 'hectares', 'winter',
   '2024-12-01', '2025-05-01', '2025-05-10',
   'harvested', 90.00, 'tons', 'Mawsim mumtaz, ghalla fawq al-mutawasit.'),

  (2, 'zone_BH_C', 'Dates',   25.00, 'hectares', 'summer',
   '2025-04-01', '2025-10-01', NULL,
   'planned', NULL, 'tons', 'Tajheez al-araish qayd al-tanfeedh.');

-- ============================================================
--  Stock (4 items per farm)
-- ============================================================
INSERT IGNORE INTO `stock`
  (`farm_id`, `name`, `category`, `quantity`, `unit`,
   `min_threshold`, `location`, `supplier`, `expiry_date`, `last_updated`)
VALUES
  
  (1, 'Budur Al-Qamh',       'Seeds',       500.00, 'kg',     100.00, 'Al-Mustawda A',           'Sharikat Al-Budur Al-Tunisiya', '2026-06-01', '2025-04-01'),
  (1, 'Samad NPK',           'Fertilizers', 300.00, 'kg',      50.00, 'Al-Mustawda B',           'Akrokeem Tunis',                '2026-12-01', '2025-04-01'),
  (1, 'Mubid Ashab X200',    'Pesticides',   80.00, 'liters',  20.00, 'Makhzan Al-Mawad Al-Kim', 'Byor Agro',                     '2025-12-01', '2025-03-15'),
  (1, 'Madakhat Ri',         'Equipment',     3.00, 'units',    1.00, 'Warsha Al-Muaddat',       'Hydrotech Tunis',               NULL,         '2025-02-01'),

 
  (2, 'Budur Al-Tamatim',    'Seeds',       200.00, 'kg',      40.00, 'Ghurfat Al-Takhzin A',    'Seed Master Tunis',             '2026-03-01', '2025-03-01'),
  (2, 'Uriya',               'Fertilizers', 150.00, 'kg',      30.00, 'Ghurfat Al-Takhzin B',    'Akrokeem Tunis',                '2026-10-01', '2025-03-01'),
  (2, 'Mubid Hashri Z50',    'Pesticides',   45.00, 'liters',  10.00, 'Makhzan Al-Mawad Al-Kim', 'Byor Agro',                     '2025-11-01', '2025-03-20'),
  (2, 'Wuqud Dizel',         'Fuel',        800.00, 'liters', 200.00, 'Khazan Al-Wuqud',         'Naft Tunis',                    NULL,         '2025-04-10');