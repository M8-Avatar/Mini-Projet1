<?php
function uploadFile($file, $destinationFolder) {
    if (empty($file['name'])) return null;

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $base = pathinfo($file['name'], PATHINFO_FILENAME);
    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $base);
    $finalName = $safeName . '_' . time() . '.' . $ext;

    move_uploaded_file($file['tmp_name'], $destinationFolder . $finalName);
    return $finalName;
}
