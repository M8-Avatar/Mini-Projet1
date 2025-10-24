<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

if (!defined('FPDF_FONTPATH')) {
    define('FPDF_FONTPATH', __DIR__ . '/../includes/fpdf/font/');
}
require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'fpdf' . DIRECTORY_SEPARATOR . 'fpdf.php';

$type = $_GET['type'] ?? 'csv';

// Récupération des vidéos
$stmt = $pdo->query("SELECT titre, categorie, description, date_publication FROM videos ORDER BY date_publication DESC");
$videos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($videos)) {
    die("Aucune donnée à exporter.");
}

// ======================================
// EXPORT CSV
// ======================================
if ($type === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="videos.csv"');

    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // En-têtes
    fputcsv($output, ['Titre', 'Catégorie', 'Description', 'Date'], ';');

    // Données
    foreach ($videos as $v) {
        fputcsv($output, [
            $v['titre'],
            $v['categorie'],
            $v['description'],
            date('d/m/Y', strtotime($v['date_publication']))
        ], ';');
    }

    fclose($output);
    exit;
}



// ======================================
// EXPORT PDF
// ======================================
if ($type === 'pdf') {
    require_once __DIR__ . '/includes/fpdf/fpdf.php';
    $pdf = new FPDF('L');
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', 'Liste des vidéos du Département'), 0, 1, 'C');
    $pdf->Ln(5);

    // Largeurs initiales des colonnes
    $widths = [110, 45, 100, 30]; // Titre, Catégorie, Description, Date

    // En-têtes
    $pdf->SetFont('Arial', 'B', 12);
    $headers = ['Titre', 'Catégorie', 'Description', 'Date de publication'];
    foreach ($headers as $i => $header) {
        $pdf->Cell($widths[$i], 10, iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $header), 1, 0, 'C');
    }
    $pdf->Ln();

    // Contenu
    $pdf->SetFont('Arial', '', 11);
    foreach ($videos as $v) {
        $titre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $v['titre']);
        $categorie = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $v['categorie']);
        $description = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $v['description']);
        $date = date('d/m/Y', strtotime($v['date_publication']));

        // Sauvegarde position Y avant écriture
        $yStart = $pdf->GetY();
        $xStart = $pdf->GetX();

        // Colonne Titre (texte multi-ligne)
        $pdf->MultiCell($widths[0], 8, $titre, 1, 'L');
        $yEnd = $pdf->GetY();
        $cellHeight = $yEnd - $yStart;
        $maxY = $yEnd;
        $pdf->SetXY($xStart + $widths[0], $yStart);

        // Colonne Catégorie
        $pdf->MultiCell($widths[1], 8, $categorie, 1, 'L');
        $yEnd = $pdf->GetY();
        $cellHeight = max($cellHeight, $yEnd - $yStart);
        $pdf->SetXY($xStart + $widths[0] + $widths[1], $yStart);

        // Colonne Description
        $pdf->MultiCell($widths[2], 8, $description, 1, 'L');
        $yEnd = $pdf->GetY();
        $cellHeight = max($cellHeight, $yEnd - $yStart);
        $pdf->SetXY($xStart + $widths[0] + $widths[1] + $widths[2], $yStart);

        // Colonne Date
        $pdf->MultiCell($widths[3], 8, $date, 1, 'C');

        // Ajustement hauteur si une colonne est plus grande que les autres
        $pdf->SetY($maxY);
    }

    if (ob_get_length()) ob_end_clean();
    $pdf->Output('D', 'videos.pdf');
    exit;
}



die("Format non supporté.");
