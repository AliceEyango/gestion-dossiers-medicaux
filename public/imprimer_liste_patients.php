<?php
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../src/Patient.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$patientModel = new Patient();
$patients = $patientModel->getAllPatients();

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Liste des patients', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
// Entêtes colonnes
$headers = [
    'Nom' => 30,
    'Prénom' => 30,
    'Date Naissance' => 25,
    'Adresse' => 50,
    'Téléphone' => 30,
    'Mutuelle' => 30
];

// Affichage des entêtes
foreach ($headers as $header => $width) {
    $pdf->Cell($width, 10, utf8_decode($header), 1, 0, 'C');
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 11);

foreach ($patients as $p) {
    // Décryptage des données
    $adresse = $patientModel->decryptData($p['adresse']);
    $telephone = $patientModel->decryptData($p['telephone']);
    $mutuelle = $patientModel->decryptData($p['mutuelle']);

    $pdf->Cell($headers['Nom'], 10, utf8_decode($p['nom']), 1);
    $pdf->Cell($headers['Prénom'], 10, utf8_decode($p['prenom']), 1);
    $pdf->Cell($headers['Date Naissance'], 10, utf8_decode($p['date_naissance']), 1);
    $pdf->Cell($headers['Adresse'], 10, utf8_decode($adresse), 1);
    $pdf->Cell($headers['Téléphone'], 10, utf8_decode($telephone), 1);
    $pdf->Cell($headers['Mutuelle'], 10, utf8_decode($mutuelle), 1);
    $pdf->Ln();
}

$pdf->Output('I', 'liste_patients.pdf');
