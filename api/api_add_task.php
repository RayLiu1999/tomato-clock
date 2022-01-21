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
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskAry = [];
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $name = test_input($data->name);
    $amount = test_input($data->amount);
    $content = test_input($data->content);
    $userId = $_SESSION['user_id'];
    $subtaskNameAry = [];
    foreach($data->subtaskName as $subtaskName) {
        array_push($subtaskNameAry, test_input($subtaskName));
    }

    createTasks($pdo, $userId, $name, $content, $amount);
    $taskId = (getTaskId($pdo))[0]['@@identity'];
    declareId($pdo);
    createPollTasks($pdo, $userId);
    if (isset($subtaskNameAry)) {
        foreach ($subtaskNameAry as $subtaskName) {
            createSubtask($pdo, $subtaskName, $userId);
        }
    }
    echo (json_encode([
        'success' => true,
        'data' => [
            'task_id' => $taskId,
        ],
        'message' => '新增任務成功'
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

    if (!isset($data)) {
        return false;
    }
    return $data;
}

function createTasks($pdo, $userId, $name, $content, $amount)
{
    $sql = "INSERT INTO tasks (user_id, name, content, amount) VALUES ('$userId', '$name', '$content', '$amount');";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function getTaskId($pdo)
{
    $sql = "SELECT @@identity";
    $pre = $pdo->prepare($sql);
    $pre->execute();
    $taskId = $pre->fetchAll(PDO::FETCH_ASSOC);
    return $taskId;
    
}

function declareId($pdo)
{
    $sql = "SET @identity = (SELECT @@identity);";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function createSubtask($pdo, $subtaskName)
{
    $sql = "INSERT INTO subtasks (name, task_id) VALUES ('$subtaskName', @identity);";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function createPollTasks($pdo, $userId)
{
    $sql = "INSERT INTO poll_tasks (task_id, user_id) VALUES (@identity, '$userId');";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}