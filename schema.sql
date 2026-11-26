-- ============================================================
-- Base de données : tasks
-- Projet : To-do list (design dashboard) + widgets bonus
-- v2 : catégories, priorité, statut à 3 états, vignette, échéance
-- ============================================================

CREATE DATABASE IF NOT EXISTS tasks
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tasks;

-- ------------------------------------------------------------
-- Utilisateurs
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50) NOT NULL UNIQUE,
  email         VARCHAR(120) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Catégories de tâches (propres à chaque utilisateur)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS task_categories (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  name       VARCHAR(80) NOT NULL,
  color      VARCHAR(20) NOT NULL DEFAULT '#F4645C',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Tâches
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS todo (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  user_id       INT NOT NULL,
  category_id   INT DEFAULT NULL,
  title         VARCHAR(150) NOT NULL,
  description   TEXT,
  priority      ENUM('faible','moderee','haute') NOT NULL DEFAULT 'moderee',
  status        ENUM('non_commence','en_cours','termine') NOT NULL DEFAULT 'non_commence',
  image_url     VARCHAR(255) DEFAULT NULL,
  due_date      DATE DEFAULT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  completed_at  TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES task_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Widgets bonus (conservés en base, non affichés dans ce design)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS content_tracker (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  title      VARCHAR(150) NOT NULL,
  type       ENUM('livre','film','serie','article','autre') NOT NULL DEFAULT 'autre',
  status     ENUM('a_faire','en_cours','termine') NOT NULL DEFAULT 'a_faire',
  rating     TINYINT DEFAULT NULL,
  notes      TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS moods (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT NOT NULL,
  mood_emoji VARCHAR(10) NOT NULL,
  note       VARCHAR(255),
  entry_date DATE NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uniq_user_date (user_id, entry_date),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;
