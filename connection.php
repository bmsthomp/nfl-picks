<?php
$config = require __DIR__ . '/config/database.php';

$con = new mysqli(
    getenv('DB_HOST') ?: $config['host'],
    getenv('DB_USER') ?: $config['username'],
    getenv('DB_PASSWORD') ?: $config['password'],
    getenv('DB_NAME') ?: $config['database']
);

// Check connection
if ($con->connect_errno) {
    echo "Failed to connect to MySQL: " . $con->connect_error;
    exit();
}