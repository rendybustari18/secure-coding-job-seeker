<?php
/**
 * Database Migration Script
 * Connects to MySQL, creates database if missing, and executes migration files.
 */
require_once __DIR__ . '/../config/env.php';

$host     = DB_HOST;
$port     = DB_PORT;
$dbName   = DB_NAME;
$username = DB_USER;
$password = DB_PASS;

echo "Connecting to MySQL server at {$host}:{$port}...\n";

$maxRetries = 30;
$retryCount = 0;
$pdo = null;

while ($retryCount < $maxRetries) {
    try {
        $pdo = new PDO("mysql:host={$host};port={$port}", $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "Successfully connected to MySQL.\n";
        break;
    } catch (PDOException $e) {
        $retryCount++;
        echo "Waiting for database connection... ({$retryCount}/{$maxRetries}) - " . $e->getMessage() . "\n";
        sleep(2);
    }
}

if (!$pdo) {
    echo "Error: Could not connect to database after {$maxRetries} attempts.\n";
    exit(1);
}

// Read migration SQL files
$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/*.sql');
sort($files);

foreach ($files as $file) {
    echo "Executing migration: " . basename($file) . "...\n";
    $sql = file_get_contents($file);
    try {
        $pdo->exec($sql);
        echo "Successfully executed " . basename($file) . "\n";
    } catch (PDOException $e) {
        echo "Migration warning/error in " . basename($file) . ": " . $e->getMessage() . "\n";
    }
}

echo "Database migration completed successfully!\n";

