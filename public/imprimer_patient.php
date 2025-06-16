<?php
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../src/Patient.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID invalide');
}

$patientModel = new Patient();
$patient = $patientModel->getPatientByIdRaw((int)$_GET['id']);

if (!$patient) {
    die('Patient introuvable');
}

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Fiche Patient', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);

function addLine($pdf, $label, $value) {
     $label = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $label);
    $value = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $value);
    $pdf->Cell(50, 10, utf8_decode($label), 0, 0);
    $pdf->Cell(0, 10, utf8_decode($value), 0, 1);
}

addLine($pdf, 'Nom :', $patient['nom']);
addLine($pdf, 'Prénom :', $patient['prenom']);
addLine($pdf, 'Date de naissance :', $patient['date_naissance']);
addLine($pdf, 'Adresse :', $patient['adresse']);
addLine($pdf, 'Téléphone :', $patient['telephone']);
addLine($pdf, 'Email :', $patient['email']);
addLine($pdf, 'N° Sécurité Sociale :', $patient['numero_securite_sociale']);
addLine($pdf, 'Mutuelle :', $patient['mutuelle']);
addLine($pdf, 'Contact d\'urgence :', $patient['personne_contact_nom'] . ' (' . $patient['personne_contact_tel'] . ')');
addLine($pdf, 'Antécédents :', $patient['antecedents_medicaux']);

$pdf->Output('I', 'fiche_patient.pdf');
