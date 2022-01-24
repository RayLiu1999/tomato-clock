<?php
session_start();
require_once '../conn.php';
header('Content-Type: application/json;charset=utf8mb4');

if (!isset($_SESSION['logged_in'])) {
    echo (json_encode([
        'success' => false,
        'message' => '尚未登入'
    ]));
}
try {
    $userId = $_SESSION['user_id'];
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $tomatoTime = test_input($data['tomatoTime']);
        $shortTime = test_input($data['shortTime']);
        $longTime = test_input($data['longTime']);
        $cycle = test_input($data['cycle']);
        $alarmSound = $data['alarmSound'];
        
        $sql = "UPDATE settings 
            SET tomato_time = '$tomatoTime', short_break_time = '$shortTime', long_break_time = '$longTime', long_break_cycle = '$cycle', ring = '$alarmSound'
            WHERE user_id = '$userId'";
        $pre = $pdo->prepare($sql);
        $pre->execute();
    
        echo(json_encode([
        'success' => true,
        'message' => '更新設定成功'
        ]));
    }
} catch (\Exception $e) {
    echo (json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = intval($data);

    if (!isset($data) || $data === 0) {
        return false;
    }
    return $data;
}

function updateTasks($pdo, $taskId, $name, $content, $amount)
{
    $sql = "UPDATE tasks SET name = '$name', content = '$content', amount = '$amount' WHERE id = '$taskId';";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function createSubtask($pdo, $subtaskName, $taskId)
{
    $sql = "INSERT INTO subtasks (name, task_id) VALUES ('$subtaskName', '$taskId');";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}
