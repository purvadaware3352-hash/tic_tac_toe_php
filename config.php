<?php
/**
 * config.php — Database connection settings
 * 
 * Update the constants below with your InfinityFree MySQL credentials.
 * You can find these in your InfinityFree Control Panel → MySQL Databases.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'tictactoe_db');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Create and return a PDO connection.
 * Uses prepared statements everywhere — no string-concatenated SQL.
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}
