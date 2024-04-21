<?php

session_start();

function isAuthenticated()
{
    return isset($_SESSION['user']);
}

function isAdmin()
{
    return isAuthenticated() && $_SESSION['user']['role'] === 'admin';
}

function isManager()
{
    return isAuthenticated() && $_SESSION['user']['role'] === 'manager';
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

function requireManager()
{
    if (!isManager()) {
        header('Location: login.php');
        exit();
    }
}
