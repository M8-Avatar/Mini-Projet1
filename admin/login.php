<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifiant = trim($_POST['identifiant'] ?? '');
    $motdepasse  = trim($_POST['motdepasse'] ?? '');

    if ($identifiant && $motdepasse) {
        $stmt = $pdo->prepare("SELECT * FROM connexion WHERE identifiant = ?");
        $stmt->execute([$identifiant]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && hash('sha256', $motdepasse) === $user['motdepasse']) {
            $_SESSION['admin'] = [
                'id' => $user['id'],
                'identifiant' => $user['identifiant']
            ];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "Identifiants invalides.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion – Espace Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: "Segoe UI", Arial, sans-serif;
    }
    .login-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 380px;
      padding: 40px;
      text-align: center;
    }
    .login-card h1 {
      color: #b30000;
      margin-bottom: 25px;
      font-size: 22px;
    }
    .btn-danger {
      background-color: #b30000;
      border: none;
    }
    .btn-danger:hover {
      background-color: #cc0000;
    }
  </style>
</head>
<body>

<div class="login-card">
  <img src="../uploads/images/Département_de_La_Réunion.png" width="80" alt="Logo">
  <h1>Connexion Admin</h1>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="post">
    <div class="mb-3">
      <input type="text" name="identifiant" class="form-control" placeholder="Identifiant" required>
    </div>
    <div class="mb-3">
      <input type="password" name="motdepasse" class="form-control" placeholder="Mot de passe" required>
    </div>
    <button type="submit" class="btn btn-danger w-100">Se connecter</button>
  </form>

  <div class="mt-3">
    <a href="/../index.php" class="text-muted small text-decoration-none">
      ← Retour à la vidéothèque
    </a>
  </div>
</div>

</body>
</html>
