<?php
// =====================
// URLs (adapter selon ton contexte)
// =====================
define('BASE_URL', '/BiblioDP');

define('PUBLIC_URL', BASE_URL);
define('ADMIN_URL',  BASE_URL . '/admin');

// =====================
// Chemins disques
// =====================
define('APP_ROOT',  dirname(__DIR__));
define('PUBLIC_PATH', APP_ROOT . '/public');
define('ADMIN_PATH',  APP_ROOT . '/admin');

define('UPLOAD_DIR', APP_ROOT . '/uploads/');
define('VIDEO_DIR',  UPLOAD_DIR . 'videos/');
define('THUMB_DIR',  UPLOAD_DIR . 'images/thumbnails/');

// Utilitaire URL vers asset
function asset_url(string $path): string {
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}
