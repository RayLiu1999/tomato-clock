<?php
session_start();
require_once '../conn.php';
header('Content-Type: application/json;charset=utf8mb4');


try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $email = $data['email'];
    $password = $data['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('信箱格式有誤');
    }

    if (empty($password)) {
        throw new Exception('密碼不得為空');
    }

    $sql = "SELECT password, id FROM users WHERE email = '".$email."'";
    $pre = $pdo->prepare($sql);
    $pre->execute();
    $user = $pre->fetch(PDO::FETCH_OBJ);

    if (empty($user)) {
        throw new Exception('帳號或密碼輸入錯誤');
    } elseif (password_verify($password, $user->password) === false) {
        throw new Exception('帳號或密碼輸入錯誤');
    } else {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user->id;
        exit(response(true, '登入成功!'));
    }
} catch (Exception $e) {
    exit(response(false, $e->getMessage()));
}

function response($boolean, $msg)
{
    return json_encode([
        'success' => $boolean,
        'message' => $msg
    ]);
}


