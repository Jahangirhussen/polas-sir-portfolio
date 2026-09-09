-- Portfolio CMS schema — single generic table drives every dashboard section.
-- Import this once via hPanel > phpMyAdmin on Hostinger.

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- section values: publications, media, achievements, education, experience,
-- projects, certifications, gallery, skills, teaching
CREATE TABLE IF NOT EXISTS content_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  section VARCHAR(40) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  data JSON NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_section (section)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default admin login: username "admin", password "ChangeMe123!"
-- CHANGE THIS PASSWORD IMMEDIATELY after first login (Dashboard > Change Password).
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$UiF9P5D6lSS3Lu33bwmOY.VDRQkTr/eE.wpi3fstQM/r2WI1A8bju');

-- Seed real known content so the dashboard isn't empty on first login.
INSERT INTO content_items (section, sort_order, data) VALUES
('experience', 1, '{"role":"Assistant Professor","org":"Asia Pacific University of Technology and Innovation (APU), Malaysia","meta":"July 2024 – Present","desc":"Research in Green Entrepreneurship, Green Energy Technology, Environment & Sustainability, and Low Carbon Emissions.","current":true}'),
('experience', 2, '{"role":"Lecturer & Researcher","org":"Sonargaon University","meta":"January 2022 – June 2024","desc":"Teaching, research supervision, and academic publication in the Department of Business Administration.","current":false}');

-- Publications and media are large datasets — after importing this schema,
-- open admin/import_seed.php once in your browser to migrate
-- data/publications.json and data/media.json into content_items automatically.
