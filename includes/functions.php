<?php
// Définition des dossiers absolus
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('VIDEO_DIR', UPLOAD_DIR . 'videos/');
define('THUMB_DIR', UPLOAD_DIR . 'images/thumbnails/');

function asset_url($path) {
    // Détecte si on est en HTTPS
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
               || $_SERVER['SERVER_PORT'] == 443;

    // Base dynamique selon le domaine
    $protocol = $isHttps ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];

    // Si tu veux forcer le domaine en production :
    // $host = 'www.departement974.fr';

    // Supprime les éventuels / initiaux dans le chemin
    $path = ltrim($path, '/');

    // Retourne l’URL complète
    return $protocol . $host . '/BiblioDP/' . $path;
}
/**
 * Gère l’upload d’un fichier de manière sécurisée et universelle.
 *
 * @param array  $file              Données du fichier ($_FILES['...'])
 * @param string $subFolder         Sous-dossier (ex: 'videos' ou 'images/thumbnails')
 * @return string|null              Nom du fichier enregistré ou null en cas d’échec
 */
function uploadFile($file, $subFolder) {
    if (empty($file['name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Détermine le dossier final à partir du sous-dossier
    $targetDir = rtrim(UPLOAD_DIR . $subFolder, '/') . '/';

    // Crée le dossier si inexistant
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Sécurise le nom du fichier
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $base = basename(pathinfo($file['name'], PATHINFO_FILENAME));
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
    $finalName = $safeName . '_' . time() . '.' . $ext;

    $targetPath = $targetDir . $finalName;

    // Déplace le fichier uploadé
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $finalName;
    }

    return null;
}
