<?php
require_once __DIR__ . '/../includes/_guard.php';
// --- HEADER ADMIN ---
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administration – Vidéothèque</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Icônes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Style global admin -->
  <style>
    body {
      font-family: "Segoe UI", Roboto, Arial, sans-serif;
      background-color: #f8f9fa;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header.admin-header {
      background-color: #b30000; /* Rouge principal */
      color: white;
      padding: 15px 40px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.2);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header.admin-header .logo {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    header.admin-header img {
      width: 75px;
      height: auto;
    }

    header.admin-header h1 {
      font-size: 22px;
      font-weight: 600;
      margin: 0;
    }

    header.admin-header nav a {
      color: #fff;
      text-decoration: none;
      margin-left: 20px;
      transition: opacity 0.2s;
      font-weight: 500;
    }

    header.admin-header nav a:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div class="logo">
    <img src="<?= asset_url('uploads/images/Département_de_La_Réunion.png') ?>" alt="Logo Département">
    <h1>Administration – Vidéothèque</h1>
  </div>

  <nav>
    <!-- <a href="../index.php"><i class="bi bi-house-door"></i> Accueil public</a> -->
    <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Tableau de bord</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Déconnexion</a>
  </nav>
</header>
