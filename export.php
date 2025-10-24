<?php
// Supprime les messages de warning/dépréciation qui cassent le PDF
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/includes/fpdf/font/');
}
require_once __DIR__ . '/includes/fpdf/fpdf.php';

$type = $_GET['type'] ?? 'csv';

// ======================================
// Récupération des vidéos AVEC catégories
// ======================================
$sql = "SELECT v.titre, c.nom AS categorie, v.description, v.date_publication
        FROM videos v
        LEFT JOIN categories c ON v.id_categorie = c.id
        ORDER BY v.date_publication DESC";

$stmt = $pdo->query($sql);
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($videos)) {
    die("Aucune donnée à exporter.");
}

// ======================================
// EXPORT CSV
// ======================================
if ($type === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=\"videos.csv\"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

    fputcsv($output, ['Titre', 'Catégorie', 'Description', 'Date'], ';');

    foreach ($videos as $v) {
        fputcsv($output, [
            $v['titre'] ?? '',
            $v['categorie'] ?? '',
            $v['description'] ?? '',
            date('d/m/Y', strtotime($v['date_publication'] ?? 'now'))
        ], ';');
    }

    fclose($output);
    exit;
}

// ======================================
// EXPORT PDF
// ======================================
if ($type === 'pdf') {
    // Important : on nettoie tout buffer avant FPDF
    if (ob_get_length()) ob_end_clean();

    $pdf = new FPDF('L');
    $pdf->AddPage();

    // Titre principal
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, mb_convert_encoding('Liste des vidéos du Département de La Réunion', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->Ln(5);

    // Largeur des colonnes
    $widths = [110, 45, 100, 30];
    $headers = ['Titre', 'Catégorie', 'Description', 'Date de publication'];

    // En-têtes
    $pdf->SetFont('Arial', 'B', 12);
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 10, mb_convert_encoding($header, 'ISO-8859-1', 'UTF-8'), 1, 0, 'C');
    }
    $pdf->Ln();

    // Données
    $pdf->SetFont('Arial', '', 11);
    foreach ($videos as $v) {
        $titre       = mb_convert_encoding($v['titre'] ?? '', 'ISO-8859-1', 'UTF-8');
        $categorie   = mb_convert_encoding($v['categorie'] ?? 'Non définie', 'ISO-8859-1', 'UTF-8');
        $description = mb_convert_encoding($v['description'] ?? '', 'ISO-8859-1', 'UTF-8');
        $date        = date('d/m/Y', strtotime($v['date_publication'] ?? 'now'));

        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        $pdf->MultiCell($widths[0], 8, $titre, 1, 'L');
        $yEnd = $pdf->GetY();
        $pdf->SetXY($xStart + $widths[0], $yStart);

        $pdf->MultiCell($widths[1], 8, $categorie, 1, 'L');
        $pdf->SetXY($xStart + $widths[0] + $widths[1], $yStart);

        $pdf->MultiCell($widths[2], 8, $description, 1, 'L');
        $pdf->SetXY($xStart + $widths[0] + $widths[1] + $widths[2], $yStart);

        $pdf->MultiCell($widths[3], 8, $date, 1, 'C');
        $pdf->SetY(max($pdf->GetY(), $yEnd));
    }

    // Envoi du PDF au navigateur
    $pdf->Output('D', 'videos.pdf');
    exit;
}

die("Format non supporté.");
