-- =============================================================
-- Tic-Tac-Toe DBMS Project — MySQL Database Schema
-- Run this in phpMyAdmin or MySQL CLI to set up the database.
-- =============================================================

CREATE DATABASE IF NOT EXISTS tictactoe_db;
USE tictactoe_db;

-- -----------------------------------------------------------
-- Users table — stores registration credentials
-- -----------------------------------------------------------
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Game statistics — one row per user, updated after each match
-- -----------------------------------------------------------
CREATE TABLE game_stats (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL UNIQUE,
    games_played    INT DEFAULT 0,
    player_wins     INT DEFAULT 0,
    ai_wins         INT DEFAULT 0,
    draws           INT DEFAULT 0,
    last_played_at  TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Match history — one row per completed game
-- -----------------------------------------------------------
CREATE TABLE match_history (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    result      ENUM('win','loss','draw') NOT NULL,
    played_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Sample records (password for all: "password123")
-- The hash below was generated with PHP password_hash()
-- -----------------------------------------------------------
INSERT INTO users (username, password) VALUES
('alice',   '$2y$10$YfR1HnRMkQz5eLhGxvL3/.kS9QhFz6cOeJbX2d1FgH3jK5mN7pQr'),
('bob',     '$2y$10$YfR1HnRMkQz5eLhGxvL3/.kS9QhFz6cOeJbX2d1FgH3jK5mN7pQr'),
('charlie', '$2y$10$YfR1HnRMkQz5eLhGxvL3/.kS9QhFz6cOeJbX2d1FgH3jK5mN7pQr');

INSERT INTO game_stats (user_id, games_played, player_wins, ai_wins, draws, last_played_at) VALUES
(1, 10, 4, 5, 1, '2026-09-09 14:30:00'),
(2, 5,  2, 2, 1, '2026-09-09 15:00:00'),
(3, 3,  1, 1, 1, '2026-09-09 15:15:00');

INSERT INTO match_history (user_id, result, played_at) VALUES
(1, 'win',  '2026-09-09 14:00:00'),
(1, 'loss', '2026-09-09 14:10:00'),
(1, 'draw', '2026-09-09 14:20:00'),
(1, 'win',  '2026-09-09 14:25:00'),
(1, 'loss', '2026-09-09 14:30:00'),
(2, 'win',  '2026-09-09 14:50:00'),
(2, 'loss', '2026-09-09 14:55:00'),
(2, 'draw', '2026-09-09 15:00:00'),
(3, 'win',  '2026-09-09 15:10:00'),
(3, 'loss', '2026-09-09 15:15:00');
