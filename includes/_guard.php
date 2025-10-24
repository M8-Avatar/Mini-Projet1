<?php
// _guard.php – protection des pages admin

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    // Redirige vers la page de connexion
    header('Location: ../admin/login.php');
    exit;
}