<?php
session_start();
include_once(__DIR__ . '/classes/Db.php');
include_once(__DIR__ . '/classes/User.php');

$db = Db::getInstance();

if (!empty($_POST)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = User::getByEmail($email, $password);

    if ($user) {
        $_SESSION["user"] = $user;

        if ($user['role'] === 'admin') {
            header('Location: managers.php');
            exit();
        } elseif ($user['role'] === 'manager') {
            header('Location: managerDashboard.php');
            exit();
        }
    } else {
        $error = "Invalid email or password.";
        // Redirect back to login page with error
        header('Location: login.php?error=' . urlencode($error));
        exit();
    }
}

