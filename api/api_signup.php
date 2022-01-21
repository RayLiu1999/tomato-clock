<?php
require_once '../conn.php';
header('Content-Type: application/json;charset=utf8mb4');

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    $email = $data['email'];
    $password = $data['password'];
    $confirm_password = $data['confirm_password'];

    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (empty($email)) {
        throw new \Exception('信箱格式有誤');
    }

    if (empty($password)) {
        throw new \Exception('請輸入密碼');
    }

    if ($password !== $confirm_password) {
        throw new \Exception('兩次密碼不相同');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['COST' => 12]);
    if ($passwordHash === false) {
        throw new \Exception('密碼有問題');
    }

    $accounts = checkUser($pdo, $email);
    if (!$accounts) {
        createUser($pdo, $email, $passwordHash);
        declareId($pdo);
        createSettings($pdo);
        echo response(true, '帳號註冊成功!');
        exit();
    } else {
        throw new \Exception('此帳號已被註冊');
    }
} catch (\Exception $e) {
    exit(response(false, $e->getMessage()));
}


function response($boolean, $msg)
{
    return json_encode([
        'success' => $boolean,
        'message' => $msg
    ]);
}

function checkUser($pdo, $email)
{
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $pre = $pdo->prepare($sql);
    $pre->execute();
    $accounts = $pre->fetch(PDO::FETCH_OBJ);

    return $accounts;
}

function createUser($pdo, $email, $passwordHash)
{
    $sql = "INSERT INTO users (email, password) VALUES ('$email', '$passwordHash')";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}


function declareId($pdo)
{
    $sql = "SET @identity = (SELECT @@identity);";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function createSettings($pdo)
{
    $sql = "INSERT INTO settings (user_id) VALUES (@identity)";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}
