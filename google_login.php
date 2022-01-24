<?php
session_start();
ob_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/conn.php';

$clientID = CLIENT_ID;
$clientSecret = CLIENT_SECRET;
$redirect_uri = explode(",", REDIRECT_URL);

// $client->setAuthConfig(__DIR__ . '/client_secret_423030282702-ua9orepd552hhusn515jsv4jvu31g6iq.apps.googleusercontent.com.json');
// $client->addScope(Google\Service\Drive::DRIVE_METADATA_READONLY);
// $client->setAccessType('offline');        // offline access
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setIncludeGrantedScopes(true);
$client->setRedirectUri("http://localhost/TomatoClock/google_login.php");
// $client->setRedirectUri(trim($redirect_uri[1]));
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token =$client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $passwordHash = password_hash(generateRandomString(), PASSWORD_DEFAULT, ['COST' => 12]);
    $name =  $google_account_info->name;
    $google_id = $google_account_info->id;
    $user = checkId($pdo, $google_id);
    if (!$user) {
        if (!checkEmail($pdo, $email)) {
            createUser($pdo, $email, $passwordHash, $google_id);
            declareId($pdo);
            createSettings($pdo);
        }
    }
    updateUser($pdo, $email, $google_id);
    
    $_SESSION['logged_in'] = true;
    $_SESSION['user_id'] = $user->id;
    header('Location:/TomatoClock');
} else {
    $google_login_url = $client->createAuthUrl();
    header('Location: ' . filter_var($google_login_url, FILTER_SANITIZE_URL));
}

function checkId($pdo, $google_id)
{
    $sql = "SELECT id FROM users WHERE google_id = '$google_id'";
    $pre = $pdo->prepare($sql);
    $pre->execute();
    $accounts = $pre->fetch(PDO::FETCH_OBJ);

    return $accounts;
}

function checkEmail($pdo, $email)
{
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $pre = $pdo->prepare($sql);
    $pre->execute();
    $accounts = $pre->fetch(PDO::FETCH_OBJ);

    return $accounts;
}

function createUser($pdo, $email, $passwordHash, $google_id)
{
    $sql = "INSERT INTO users (email, password, google_id) VALUES ('$email', '$passwordHash', '$google_id')";
    $pre = $pdo->prepare($sql);
    $pre->execute();
}

function updateUser($pdo, $email, $google_id) {
    $sql = "UPDATE users SET google_id = '$google_id' WHERE email = '$email'";
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

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}