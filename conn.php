<?php
require_once __DIR__ . '/config.php';
$hostname = DB_HOST;
$username = DB_USER;
$password = DB_PASS;
$db_name = DB_NAME;
$dsn = "mysql:host=$hostname;port=3306;dbname=$db_name;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );
} catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}