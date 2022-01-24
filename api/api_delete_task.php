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
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (isset($data['task_id'])) {
            $taskId = $data['task_id'];
            function deleteTask($pdo, $taskId)
            {
                $string = join("," ,$taskId);
                $sql = "DELETE FROM tasks WHERE id IN ($string)";
                $pre = $pdo->prepare($sql);
                $pre->execute();
            }
        
            deleteTask($pdo, $taskId);
            echo (json_encode([
                'success' => true,
                'message' => '刪除任務成功'
            ]));
            exit();
        }
        
        
        $userId = $_SESSION['user_id'];
        $sql = "DELETE FROM tasks WHERE user_id='$userId'";
        $pre = $pdo->prepare($sql);
        $pre->execute();
        
        echo (json_encode([
            'success' => true,
            'message' => '刪除全部任務成功'
        ]));
    }
} catch (\Exception $e) {
    echo (json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]));
}