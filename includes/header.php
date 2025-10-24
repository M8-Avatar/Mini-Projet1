<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Plateforme vidéo du Département</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="header-bar">
  <div class="container d-flex justify-content-between align-items-center py-2">
    <div class="d-flex align-items-center">
      <img src="uploads/images/DP.ico" alt="Logo" class="logo me-2">
      <h1 class="site-title mb-0">Département de La Réunion</h1>
    </div>
    <form class="search-bar d-flex" method="get" action="index.php">
      <input type="text" name="search" class="form-control" placeholder="Rechercher une vidéo..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
      <button class="btn btn-light ms-2" type="submit"><i class="bi bi-search"></i></button>
    </form>
  </div>
</header>