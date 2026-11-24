<?php
// Database configuration
define('DB_HOST', $_ENV['DB_HOST'] ?? 'mysql');
define('DB_PORT', $_ENV['DB_PORT'] ?? '3306');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'db_job_seeker');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'root');

// JWT Secret
define('JWT_SECRET', 'weak_secret_key_123');

// Mailtrap configuration
define('MAILTRAP_HOST', 'sandbox.smtp.mailtrap.io');
define('MAILTRAP_PORT', 2525);
define('MAILTRAP_USERNAME', 'c422d05e0331d3');
define('MAILTRAP_PASSWORD', '76eae054995016');

// Upload config
define('UPLOAD_MAX_SIZE', 50 * 1024 * 1024); // 50MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'php', 'exe']);
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');

// Base URL
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost:8004');
define('FRONTEND_URL', $_ENV['FRONTEND_URL'] ?? 'http://localhost:5173');

