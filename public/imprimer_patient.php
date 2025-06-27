  <?php
require_once __DIR__ . '/../lib/fpdf.php';
require_once __DIR__ . '/../src/Patient.php';
require_once __DIR__ . '/../config/config.php';

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID invalide');
}

$patientModel = new Patient();
$patient = $patientModel->getPatientByIdRaw((int)$_GET['id']);

if (!$patient) {
    die('Patient introuvable');
}

$key = $_SESSION['cle_dechiffrement'] ?? null;

$isDecrypted = ($key && $key === ENCRYPTION_KEY);

if ($isDecrypted) {
    $patient['adresse'] = $patientModel->decryptData($patient['adresse']);
    $patient['telephone'] = $patientModel->decryptData($patient['telephone']);
    $patient['email'] = $patientModel->decryptData($patient['email']);
    $patient['numero_securite_sociale'] = $patientModel->decryptData($patient['numero_securite_sociale']);
    $patient['mutuelle'] = $patientModel->decryptData($patient['mutuelle']);
    $patient['personne_contact_nom'] = $patientModel->decryptData($patient['personne_contact_nom']);
    $patient['personne_contact_tel'] = $patientModel->decryptData($patient['personne_contact_tel']);
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
addLine($pdf, 'Prenom :', $patient['prenom']);
addLine($pdf, 'sexe :', $patient['sexe']);
addLine($pdf, 'Date de naissance :', $patient['date_naissance']);
addLine($pdf, 'Adresse :', $patient['adresse']);
addLine($pdf, 'Telephone :', $patient['telephone']);
addLine($pdf, 'Email :', $patient['email']);
addLine($pdf, 'N Securite Sociale :', $patient['numero_securite_sociale']);
addLine($pdf, 'Mutuelle :', $patient['mutuelle']);
addLine($pdf, 'Contact d\'urgence :', $patient['personne_contact_nom'] . ' (' . $patient['personne_contact_tel'] . ')');


unset($_SESSION['cle_dechiffrement']); // On supprime la clé après usage

$pdf->Output('I', 'fiche_patient.pdf');


exit;
?> 
