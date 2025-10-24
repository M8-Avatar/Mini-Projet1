<?php
require_once __DIR__ . '/../includes/_guard.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Vérifie la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

// Champs principaux
$titre        = trim($_POST['titre'] ?? '');
$description  = trim($_POST['description'] ?? '');
$idCategorie  = $_POST['id_categorie'] ?? null;
$newCategorie = trim($_POST['new_categorie'] ?? '');

// Étape 1 : Vérifie ou crée la catégorie
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

// Si aucune catégorie valide → on met “Autre” par défaut
if ($idCategorie <= 0) {
    $stmtDefault = $pdo->prepare("SELECT id FROM categories WHERE nom = 'Autre'");
    $stmtDefault->execute();
    $idCategorie = $stmtDefault->fetchColumn();

    if (!$idCategorie) {
        $pdo->prepare("INSERT INTO categories (nom) VALUES ('Autre')")->execute();
        $idCategorie = $pdo->lastInsertId();
    }
}

// ===============================
//  UPLOADS VIDÉO ET MINIATURE
// ===============================

// Vidéo obligatoire
$videoFile = uploadFile($_FILES['video'], 'videos');
if (!$videoFile) {
    die("Erreur : le fichier vidéo n’a pas pu être importé.");
}

// Miniature (optionnelle)
$thumbnailFile = uploadFile($_FILES['miniature'], 'images/thumbnails');

// ===============================
//  ENREGISTREMENT EN BASE
// ===============================
$stmt = $pdo->prepare("
    INSERT INTO videos (titre, description, fichier, miniature, id_categorie)
    VALUES (:titre, :description, :fichier, :miniature, :id_categorie)
");

$stmt->execute([
    ':titre'        => $titre ?: 'Vidéo sans titre',
    ':description'  => $description,
    ':fichier'      => $videoFile,
    ':miniature'    => $thumbnailFile,
    ':id_categorie' => $idCategorie
]);

header('Location: ../index.php?success=1');
exit;
