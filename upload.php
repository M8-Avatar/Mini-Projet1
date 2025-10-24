<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uploadDir = "videos/";
    $thumbnailDir = "images/thumbnails/";

    // Vérifier et créer les dossiers si nécessaire
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (!is_dir($thumbnailDir)) {
        mkdir($thumbnailDir, 0777, true);
    }

    // Vérifier les fichiers
    if (!empty($_FILES["video-file"]["name"]) && !empty($_FILES["video-thumbnail"]["name"])) {
        $videoName = basename($_FILES["video-file"]["name"]);
        $videoPath = $uploadDir . $videoName;
        $thumbnailName = pathinfo($videoName, PATHINFO_FILENAME) . ".jpg";
        $thumbnailPath = $thumbnailDir . $thumbnailName;
        
        // Déplacer la vidéo
        if (move_uploaded_file($_FILES["video-file"]["tmp_name"], $videoPath)) {
            // Déplacer la miniature
            if (move_uploaded_file($_FILES["video-thumbnail"]["tmp_name"], $thumbnailPath)) {
                echo "Vidéo et miniature téléchargées avec succès.";
            } else {
                echo "Erreur lors de l'upload de la miniature.";
            }
        } else {
            echo "Erreur lors de l'upload de la vidéo.";
        }
    } else {
        echo "Veuillez sélectionner un fichier vidéo et une miniature.";
    }
} else {
    echo "Méthode non autorisée.";
}
?>
