<?php
require_once __DIR__ . '/../config/config.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}


$cle = $_SESSION['cle_dechiffrement'] ?? null;
$message = '';

require_once __DIR__ . '/../src/Patient.php';
$patientModel = new Patient();

$message = '';
$patient = [
    'id' => '',
    'nom' => '',
    'prenom' => '',
    'date_naissance' => '',
    'adresse' => '',
    'telephone' => '',
    'email' => '',
    'numero_securite_sociale' => '',
    'mutuelle' => '',
    'personne_contact_nom' => '',
    'personne_contact_tel' => '',
    'antecedents_medicaux' => ''
];

// Vérifie si un ID a été passé
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = (int)$_GET['id'];
$patient = $patientModel->getPatientById($id);

if (!$patient) {
    $message = "Patient introuvable.";
}

if ($cle !== ENCRYPTION_KEY) {
    echo "<p style='color:red;'>Clé incorrecte. Vous ne pouvez pas modifier les données.</p>";
    exit;
}

unset($_SESSION['cle_dechiffrement']);

// Traitement du formulaire


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $email = $_POST['email'] ?? '';
    $numero_ss = $_POST['numero_ss'] ?? '';
    $mutuelle = $_POST['mutuelle'] ?? '';
    $contact_nom = $_POST['contact_nom'] ?? '';
    $contact_tel = $_POST['contact_tel'] ?? '';
    $antecedents = $_POST['antecedents'] ?? '';

    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("
        UPDATE patients SET 
            nom = ?, prenom = ?, date_naissance = ?,
            adresse = ?, telephone = ?, email = ?, 
            numero_securite_sociale = ?, mutuelle = ?, 
            personne_contact_nom = ?, personne_contact_tel = ?, 
            antecedents_medicaux = ?
        WHERE id = ?
    ");

    $ok = $stmt->execute([
        $nom,
        $prenom,
        $date_naissance,
        $patientModel->encryptData($adresse),
        $patientModel->encryptData($telephone),
        $patientModel->encryptData($email),
        $patientModel->encryptData($numero_ss),
        $patientModel->encryptData($mutuelle),
        $patientModel->encryptData($contact_nom),
        $patientModel->encryptData($contact_tel),
        $patientModel->encryptData($antecedents),
        $id
    ]);

    if ($ok) {
          header('Location: dashboard.php?page=patients');
        exit;
    } else {
        $message = "Erreur lors de la mise à jour.";
    }
} 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
<div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h2 class="text-2xl font-semibold mb-4 text-blue-700">Modifier les informations du patient</h2>

    <?php if ($message): ?>
        <p class="text-red-600 mb-4"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="post" class="grid grid-cols-2 gap-4">
        <input type="text" name="nom" value="<?= htmlspecialchars($patient['nom']) ?>" required class="border p-2 rounded">
        <input type="text" name="prenom" value="<?= htmlspecialchars($patient['prenom']) ?>" required class="border p-2 rounded">
        <input type="date" name="date_naissance" value="<?= htmlspecialchars($patient['date_naissance']) ?>" required class="border p-2 rounded col-span-2">
        <input type="text" name="adresse" value="<?= htmlspecialchars($patient['adresse']) ?>" class="border p-2 rounded col-span-2">
        <input type="text" name="telephone" value="<?= htmlspecialchars($patient['telephone']) ?>" class="border p-2 rounded">
        <input type="email" name="email" value="<?= htmlspecialchars($patient['email']) ?>" class="border p-2 rounded">
        <input type="text" name="numero_ss" value="<?= htmlspecialchars($patient['numero_securite_sociale']) ?>" class="border p-2 rounded col-span-2">
        <input type="text" name="mutuelle" value="<?= htmlspecialchars($patient['mutuelle']) ?>" class="border p-2 rounded col-span-2">
        <input type="text" name="contact_nom" value="<?= htmlspecialchars($patient['personne_contact_nom']) ?>" class="border p-2 rounded">
        <input type="text" name="contact_tel" value="<?= htmlspecialchars($patient['personne_contact_tel']) ?>" class="border p-2 rounded">
        <textarea name="antecedents" rows="3" class="border p-2 rounded col-span-2"><?= htmlspecialchars($patient['antecedents_medicaux']) ?></textarea>

        <div class="col-span-2 flex justify-between mt-4">
            <a href="dashboard.php?page=patients" class="text-blue-600 hover:underline">← Retour</a>
            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">Enregistrer</button>
        </div>
    </form>
</div>
</body>
</html>
