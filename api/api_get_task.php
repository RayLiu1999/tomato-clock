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
    $userId = $_SESSION['user_id'];
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        function getTasks($pdo, $userId)
        {
            $sql = "SELECT id, name, amount, progress FROM tasks WHERE user_id = '$userId'";
            $pre = $pdo->prepare($sql);
            $pre->execute();
            $tasks = $pre->fetchAll(PDO::FETCH_ASSOC);
            return $tasks;
        }

        $tasks = getTasks($pdo, $userId);
        echo(json_encode([
        'success' => true,
        'data' => $tasks
    ]));
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $taskId = $data['task_id'];

        function getSubtask($pdo, $taskId)
        {
            $sql = "SELECT subtasks.id ,subtasks.name FROM tasks JOIN subtasks ON tasks.id = subtasks.task_id WHERE tasks.id = '$taskId'";
            $pre = $pdo->prepare($sql);
            $pre->execute();
            $subtask = $pre->fetchAll(PDO::FETCH_ASSOC);
            return $subtask;
        }

        function getTask($pdo, $taskId)
        {
            $sql = "SELECT id, name, content, amount, progress FROM tasks WHERE id = '$taskId'";
            $pre = $pdo->prepare($sql);
            $pre->execute();
            $task = $pre->fetch(PDO::FETCH_ASSOC);
            return $task;
        }

        $task = getTask($pdo, $taskId);
        $subtask = getSubtask($pdo, $taskId);
        $task['subtask'] = $subtask;

        echo(json_encode([
        'success' => true,
        'data' => $task
    ]));
        exit();
    }
} catch (\Exception $e) {
    echo (json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}