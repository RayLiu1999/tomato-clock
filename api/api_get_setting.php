<?php
session_start();
require_once '../conn.php';
header('Content-Type: application/json;charset=utf8mb4');

if (!isset($_SESSION['logged_in'])) {
    echo (json_encode([
        'success' => false,
        'message' => '尚未登入'
    ]));
    exit();
}
try {
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $userId = $_SESSION['user_id'];
        $sql = "SELECT * FROM settings WHERE user_id = '$userId'";
        $pre = $pdo->prepare($sql);
        $pre->execute();
        $setting = $pre->fetch(PDO::FETCH_ASSOC);
        
        echo (json_encode([
            'success' => true,
            'data' => $setting
        ]));
    }
} catch (\Exception $e) {
    echo (json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}