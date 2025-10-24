<?php
require_once 'includes/db.php';
$type = $_GET['type'] ?? 'csv';
$stmt = $pdo->query("SELECT * FROM videos ORDER BY date_publication DESC");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($type === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="videos.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Titre', 'Catégorie', 'Date']);
    foreach ($videos as $v) {
        fputcsv($output, [$v['titre'], $v['categorie'], $v['date_publication']]);
    }
    fclose($output);
    exit;
}
