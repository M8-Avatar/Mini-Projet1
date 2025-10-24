<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Vérifie la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Champs principaux
$titre        = trim($_POST['titre'] ?? '');
$description  = trim($_POST['description'] ?? '');
$idCategorie  = $_POST['categorie'] ?? null;
$newCategorie = trim($_POST['new_categorie'] ?? '');

// --- Étape 1 : vérifier ou créer la catégorie ---
if ($idCategorie === '__new__' && !empty($newCategorie)) {
    // Vérifie si la nouvelle catégorie existe déjà
    $check = $pdo->prepare("SELECT id FROM categories WHERE nom = ?");
    $check->execute([$newCategorie]);
    $existingId = $check->fetchColumn();

    if ($existingId) {
        $idCategorie = $existingId;
    } else {
        $insertCat = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
        $insertCat->execute([$newCategorie]);
        $idCategorie = $pdo->lastInsertId();
    }
} else {
    $idCategorie = (int) $idCategorie;
}

// --- Étape 2 : vérifier si la catégorie est valide ---
if ($idCategorie <= 0) {
    // On essaie de trouver "Autre"
    $stmtDefault = $pdo->prepare("SELECT id FROM categories WHERE nom = 'Autre'");
    $stmtDefault->execute();
    $idCategorie = $stmtDefault->fetchColumn();

    // Si elle n'existe pas encore, on la crée
    if (!$idCategorie) {
        $pdo->prepare("INSERT INTO categories (nom) VALUES ('Autre')")->execute();
        $idCategorie = $pdo->lastInsertId();
    }
}

// --- Étape 3 : gestion des dossiers ---
$videosDir = __DIR__ . '/uploads/videos/';
$thumbsDir = __DIR__ . '/uploads/images/thumbnails/';
if (!is_dir($videosDir)) mkdir($videosDir, 0777, true);
if (!is_dir($thumbsDir)) mkdir($thumbsDir, 0777, true);

// --- Étape 4 : vérification du fichier vidéo ---
if (!isset($_FILES['video']) || $_FILES['video']['error'] !== UPLOAD_ERR_OK) {
    die("Erreur : aucune vidéo valide reçue.");
}

// Nettoyage du nom du fichier vidéo
$videoName = pathinfo($_FILES['video']['name'], PATHINFO_FILENAME);
$videoExt  = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
$videoSafe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $videoName) . '.' . $videoExt;
move_uploaded_file($_FILES['video']['tmp_name'], $videosDir . $videoSafe);

// --- Étape 5 : miniature (facultative) ---
$miniName = null;
if (!empty($_FILES['miniature']['name']) && $_FILES['miniature']['error'] === UPLOAD_ERR_OK) {
    $miniExt  = strtolower(pathinfo($_FILES['miniature']['name'], PATHINFO_EXTENSION));
    $miniName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $videoName) . '.' . $miniExt;
    move_uploaded_file($_FILES['miniature']['tmp_name'], $thumbsDir . $miniName);
}

// --- Étape 6 : enregistrement en base ---
$stmt = $pdo->prepare("
    INSERT INTO videos (titre, description, fichier, miniature, id_categorie)
    VALUES (:titre, :description, :fichier, :miniature, :id_categorie)
");

$stmt->execute([
    ':titre'        => $titre ?: 'Vidéo sans titre',
    ':description'  => $description,
    ':fichier'      => $videoSafe,
    ':miniature'    => $miniName,
    ':id_categorie' => $idCategorie
]);

header('Location: index.php?success=1');
exit;
