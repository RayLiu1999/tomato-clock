<?php
session_start();
require_once '../conn.php';
header('Content-Type: application/json;charset=utf8mb4');
date_default_timezone_set('Asia/Taipei');

if (!isset($_SESSION['logged_in'])) {
    echo (json_encode([
        'success' => false,
        'message' => '尚未登入'
    ]));
    exit();
}
try {
$userId = $_SESSION['user_id'];
// 0 -> 6 從星期日開始
$weekData = [0, 0, 0, 0, 0, 0, 0];
$thisWeekMonday = date("Y-m-d", time() - 86400 * (date("w") - 1));

$sql = "SELECT progress, created_at FROM poll_tasks WHERE user_id = '$userId'";
$pre = $pdo->prepare($sql);
$pre->execute();
$datas = $pre->fetchAll(PDO::FETCH_OBJ);
foreach ($datas as $data) {
    if ($thisWeekMonday <= $data->created_at) {
        $weekData[date("w", strtotime($data->created_at))] += $data->progress;
    }
}

echo (json_encode([
    'success' => true,
    'data' => $weekData
]));

} catch (\Exception $e) {
    echo (json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}
