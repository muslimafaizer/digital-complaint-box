<?php
// config.php
// DB connection settings for XAMPP (default)
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';         // XAMPP default is blank
$DB_NAME = 'dcbs_db';

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// set charset
$conn->set_charset('utf8mb4');
