<?php
session_start();
require 'vendor/autoload.php';

use PHPGangsta\GoogleAuthenticator;

$ga = new GoogleAuthenticator();

if (!isset($_SESSION['2fa_secret'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'];
    $secret = $_SESSION['2fa_secret'];

    if ($ga->verifyCode($secret, $code, 2)) { // 2 = 2*30sec clock tolerance
        $_SESSION['user_id'] = $_SESSION['2fa_user_id'];
        unset($_SESSION['2fa_user_id']);
        unset($_SESSION['2fa_secret']);
        header('Location: dashboard.php');
        exit();
    } else {
        echo 'Invalid 2FA code.';
    }
}
?>

<form method="POST">
    <label for="code">Enter your 2FA code:</label>
    <input type="text" id="code" name="code" required>
    <button type="submit">Verify</button>
</form>
