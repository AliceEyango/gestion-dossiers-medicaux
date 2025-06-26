<?php
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../src/DossierMedical.php';
require_once __DIR__ . '/../src/Patient.php';
session_start();

if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    die('Accès refusé');
}

$dossierModel = new DossierMedical();
$dechiffreur = new Patient();
$dossier = $dossierModel->getDossierById($_GET['id']);

$key = $_SESSION['cle_dechiffrement'] ?? null;
if ($key && $key === ENCRYPTION_KEY) {
    $dossier['diagnostic'] = $dechiffreur->decryptData($dossier['diagnostic']);
    $dossier['traitement'] = $dechiffreur->decryptData($dossier['traitement']);
    $dossier['commentaire'] = $dechiffreur->decryptData($dossier['commentaire']);
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Dossier Médical', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);

$pdf->Ln(10);
$pdf->Cell(50, 10, 'Date:', 0, 0);
$pdf->Cell(0, 10, $dossier['date_consultation'], 0, 1);
$pdf->Cell(50, 10, 'Diagnostic:', 0, 0);
$pdf->MultiCell(0, 10, $dossier['diagnostic']);
$pdf->Cell(50, 10, 'Traitement:', 0, 0);
$pdf->MultiCell(0, 10, $dossier['traitement']);
$pdf->Cell(50, 10, 'Commentaire:', 0, 0);
$pdf->MultiCell(0, 10, $dossier['commentaire']);

$pdf->Output('I', 'dossier_medical.pdf');
exit;
?>