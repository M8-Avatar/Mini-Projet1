<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Vérifie la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

// Récupération sécurisée
$titre = trim($_POST['titre'] ?? '');
$description = trim($_POST['description'] ?? '');
$categorie = trim($_POST['categorie'] ?? 'Autre');

$videosDir = __DIR__ . '/uploads/videos/';
$thumbsDir = __DIR__ . '/uploads/images/thumbnails/';
if (!is_dir($videosDir)) mkdir($videosDir, 0777, true);
if (!is_dir($thumbsDir)) mkdir($thumbsDir, 0777, true);

// Vérifie le fichier vidéo
$videoFile = $_FILES['video'] ?? null;
if (!$videoFile || $videoFile['error'] !== UPLOAD_ERR_OK) {
    die("⚠️ Erreur lors de l'envoi de la vidéo (fichier non reçu).");
}

// Génère un nom de fichier propre
$videoName = pathinfo($videoFile['name'], PATHINFO_FILENAME);
$videoExt = strtolower(pathinfo($videoFile['name'], PATHINFO_EXTENSION));
$videoSafe = preg_replace('/[^a-zA-Z0-9_-]/', '_', $videoName) . '.' . $videoExt;

// Déplace la vidéo
move_uploaded_file($videoFile['tmp_name'], $videosDir . $videoSafe);

// Miniature (facultative)
$miniName = null;
if (!empty($_FILES['miniature']['name']) && $_FILES['miniature']['error'] === UPLOAD_ERR_OK) {
    $miniExt = strtolower(pathinfo($_FILES['miniature']['name'], PATHINFO_EXTENSION));
    $miniName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $videoName) . '.' . $miniExt;
    move_uploaded_file($_FILES['miniature']['tmp_name'], $thumbsDir . $miniName);
}

// Enregistre en base
$stmt = $pdo->prepare("INSERT INTO videos (titre, description, categorie, fichier, miniature)
                       VALUES (:titre, :description, :categorie, :fichier, :miniature)");
$stmt->execute([
    ':titre' => $titre ?: 'Vidéo sans titre',
    ':description' => $description,
    ':categorie' => $categorie,
    ':fichier' => $videoSafe,
    ':miniature' => $miniName
]);

header('Location: index.php?success=1');
exit;
