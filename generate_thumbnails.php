<?php
$videoDir = "/var/www/html/projet-videos-DP/videos/";  // Dossier contenant les vidéos
$thumbnailDir = "/var/www/html/projet-videos-DP/images/thumbnails/";  // Dossier où enregistrer les miniatures

// Vérifier si le dossier des miniatures existe, sinon le créer
if (!is_dir($thumbnailDir)) {
    mkdir($thumbnailDir, 0777, true);
}

// Récupérer toutes les vidéos MP4
$videos = glob($videoDir . "*.mp4");

foreach ($videos as $video) {
    $filename = pathinfo($video, PATHINFO_FILENAME);
    $thumbnailPath = $thumbnailDir . $filename . ".jpg";

    // Vérifier si la miniature existe déjà
    if (!file_exists($thumbnailPath)) {
        // Commande FFmpeg pour extraire une image à 5 secondes
        $command = "ffmpeg -i \"$video\" -ss 00:00:05 -vframes 1 \"$thumbnailPath\" -y 2>&1";
        $output = shell_exec($command);

        // Vérification du résultat
        if (file_exists($thumbnailPath)) {
            echo "Miniature créée : $thumbnailPath\n";
        } else {
            echo "Erreur lors de la création de la miniature pour $video\n";
            echo "FFmpeg output: $output\n";
        }
    } else {
        echo "Miniature déjà existante : $thumbnailPath\n";
    }
}

echo "Génération des miniatures terminée !\n";
?>

