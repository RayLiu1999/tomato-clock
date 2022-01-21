<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createMutable(__DIR__);
$dotenv->load();

define("DB_HOST", $_ENV['DB_HOST']);
define("DB_NAME", $_ENV['DB_NAME']);
define("DB_USER", $_ENV['DB_USER']);
define("DB_PASS", $_ENV['DB_PASS']);
define("CLIENT_ID", $_ENV['CLIENT_ID']);
define("CLIENT_SECRET", $_ENV['CLIENT_SECRET']);
define("REDIRECT_URL", $_ENV['REDIRECT_URL']);