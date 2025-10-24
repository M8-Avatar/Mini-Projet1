<?php
require_once __DIR__ . '/../includes/db.php';

if (!isset($_GET['id'])) {
    die("Aucune vidéo spécifiée.");
}

$id = (int) $_GET['id'];

// Récupère le nom du fichier avant suppression
$stmt = $pdo->prepare("SELECT fichier, miniature FROM videos WHERE id = ?");
$stmt->execute([$id]);
$video = $stmt->fetch(PDO::FETCH_ASSOC);

if ($video) {
    // Supprime le fichier vidéo
    $videoPath = __DIR__ . '/uploads/videos/' . $video['fichier'];
    if (file_exists($videoPath)) unlink($videoPath);

    // Supprime la miniature
    if (!empty($video['miniature'])) {
        $thumbPath = __DIR__ . '/uploads/images/thumbnails/' . $video['miniature'];
        if (file_exists($thumbPath)) unlink($thumbPath);
    }

    // Supprime l’entrée dans la base
    $delete = $pdo->prepare("DELETE FROM videos WHERE id = ?");
    $delete->execute([$id]);
}

header('Location: index.php');
exit;
