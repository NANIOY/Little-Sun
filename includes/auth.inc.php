<?php
session_save_path(__DIR__ . '/../sessions');
session_start();

error_log('Session data in auth.inc.php: ' . print_r($_SESSION, true));

function isAuthenticated()
{
    error_log('isAuthenticated: ' . isset($_SESSION['user']));
    return isset($_SESSION['user']);
}

function isAdmin()
{
    error_log('isAdmin: ' . (isAuthenticated() && $_SESSION['user']['role'] === 'admin'));
    return isAuthenticated() && $_SESSION['user']['role'] === 'admin';
}

function isManager()
{
    return isAuthenticated() && $_SESSION['user']['role'] === 'manager';
}

function isWorker()
{
    return isAuthenticated() && $_SESSION['user']['role'] === 'worker';
}

function requireAuth()
{
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin()
{
    error_log('requireAdmin');
    if (!isAdmin()) {
        header('Location: login.php');
        exit();
    }
}

function requireManager()
{
    if (!isManager()) {
        header('Location: login.php');
        exit();
    }
}

function requireWorker()
{
    if (!isWorker()) {
        header('Location: login.php');
        exit();
    }
}
