<?php

require 'koneksi.php';

session_start();

function isLoggedIn()
{
    return isset($_SESSION['status_login']) && $_SESSION['status_login'];
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function isAdmin()
{
    return isLoggedIn() && $_SESSION['level'] === 'admin';
}

function logout()
{
    session_destroy();
    session_reset();
    header('Location: login.php');
    exit();
}
