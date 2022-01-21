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
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $taskAry = [];
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        
        if (isset($data->progress)) {
            $progress = $data->progress;
            $taskId = $data->task_id;
            updateProgress($pdo, $taskId, $progress);
            updatePollProgress($pdo, $taskId, $progress);
            getAmount($pdo, $taskId);
            echo (json_encode([
                'success' => true,
                'data' => getAmount($pdo, $taskId)
            ]));
            exit();
        }

        $name = test_input($data->name);
        $amount = test_input($data->amount);
        $content = test_input($data->content);
        $taskId = $data->task_id;
        $subtaskNameAry = [];
        foreach ($data->subtaskName as $subtaskName) {
            array_push($subtaskNameAry, test_input($subtaskName));
        }

        updateTasks($pdo, $taskId, $name, $content, $amount);

        if (isset($subtaskNameAry)) {
            foreach ($subtaskNameAry as $subtaskName) {
                createSubtask($pdo, $subtaskName, $taskId);
            }
        }

        echo (json_encode([
            'success' => true,
            'message' => '更新任務成功'
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

function updateProgress($pdo, $taskId, $progress)
{
    $sql = "UPDATE tasks SET progress = '$progress' WHERE id = '$taskId';";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function getAmount($pdo, $taskId) 
{
    $sql = "SELECT amount FROM tasks WHERE id = '$taskId';";
    $pre = $pdo->prepare($sql);
    $pre->execute();
    $amount = $pre->fetch(PDO::FETCH_ASSOC);
    return $amount;
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


function updatePollProgress($pdo, $taskId, $progress)
{
    $sql = "UPDATE poll_tasks SET progress = '$progress' WHERE task_id = '$taskId';";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}