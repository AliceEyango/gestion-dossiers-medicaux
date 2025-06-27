<?php
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../src/DossierMedical.php';
require_once __DIR__ . '/../src/Patient.php';
require_once __DIR__ . '/../config/config.php';

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID invalide');
}

$dossierModel = new DossierMedical();
$dossier = $dossierModel->getDossierByIdRaw((int)$_GET['id']);

if (!$dossier) {
    die('Dossier introuvable');
}

$patientModel = new Patient();
$patient = $patientModel->getPatientByIdRaw($dossier['patient_id']);

$key = $_SESSION['cle_dechiffrement'] ?? null;
$isDecrypted = ($key && $key === ENCRYPTION_KEY);

if ($isDecrypted) {
    $dossier['diagnostic'] = $patientModel->decryptData($dossier['diagnostic']);
    $dossier['traitement'] = $patientModel->decryptData($dossier['traitement']);
    $dossier['commentaire'] = $patientModel->decryptData($dossier['commentaire']);
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Fiche Patient', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);

function addLine($pdf, $label, $value) {
    $label = mb_convert_encoding($label, 'ISO-8859-1', 'UTF-8');
    $value = mb_convert_encoding($value, 'ISO-8859-1', 'UTF-8');
    $pdf->Cell(50, 10, $label, 0, 0);
    $pdf->Cell(0, 10, $value, 0, 1);
}

// Ajouter nom/prénom du patient
addLine($pdf, 'Nom :', $patient['nom']);
addLine($pdf, 'Prenom :', $patient['prenom']);

addLine($pdf, 'Date :', $dossier['date_consultation']);
addLine($pdf, 'Diagnostic :', $dossier['diagnostic']);
addLine($pdf, 'Traitement :', $dossier['traitement']);
addLine($pdf, 'Commentaire :', $dossier['commentaire']);



unset($_SESSION['cle_dechiffrement']);
ob_clean();
$pdf->Output('I', 'dossier_medical.pdf');
exit;

?> 
