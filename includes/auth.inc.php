<?php

session_start();

function isAuthenticated()
{
    return isset($_SESSION['user']);
}

function isAdmin()
{
    return isAuthenticated() && $_SESSION['user']['role'] === 'Admin';
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
    if (!isAdmin()) {
        header('Location: login.php');
        exit();
    }
}
