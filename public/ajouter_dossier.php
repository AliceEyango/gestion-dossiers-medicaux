<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../src/Patient.php';
require_once __DIR__ . '/../src/DossierMedical.php';
require_once __DIR__ . '/../config/config.php';

$patientModel = new Patient();
$dossierModel = new DossierMedical();
$patients = $patientModel->getAllPatients();

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $date_consultation = $_POST['date_consultation'] ?? '';
    $diagnostic = $_POST['diagnostic'] ?? '';
    $traitement = $_POST['traitement'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';

    // Vérifie la clé de chiffrement en session
    $key = $_SESSION['cle_dechiffrement'] ?? null;

    // Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'] ?? '';
    $date_consultation = $_POST['date_consultation'] ?? '';
    $diagnostic = $_POST['diagnostic'] ?? '';
    $traitement = $_POST['traitement'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';

    // Insertion
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT INTO dossier_medical (patient_id, date_consultation, diagnostic, traitement, commentaire) VALUES (?, ?, ?, ?, ?)");

    $ok = $stmt->execute([
    $patient_id,
    $date_consultation,
    $patientModel->encryptData($diagnostic),
    $patientModel->encryptData($traitement),
    $patientModel->encryptData($commentaire),
    ]);

        if ($ok) {
            header('Location: dashboard.php?page=dossiers');
            exit;
        } else {
            $message = "Erreur lors de l'ajout du dossier médical.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un dossier médical</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4 text-blue-700">Ajouter un dossier médical</h2>

        <?php if ($message): ?>
            <p class="text-red-600 mb-4"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label for="patient_id" class="block text-gray-700 mb-1">Patient :</label>
                <select name="patient_id" id="patient_id" required class="border p-2 rounded w-full">
                    <option value="">-- Sélectionner --</option>
                    <?php foreach ($patients as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-span-2">
                <label for="date_consultation" class="block text-gray-700 mb-1">Date de consultation :</label>
                <input type="date" name="date_consultation" id="date_consultation" required class="border p-2 rounded w-full">
            </div>

            <div class="col-span-2">
                <label for="diagnostic" class="block text-gray-700 mb-1">Diagnostic :</label>
                <textarea name="diagnostic" id="diagnostic" rows="3" required class="border p-2 rounded w-full"></textarea>
            </div>

            <div class="col-span-2">
                <label for="traitement" class="block text-gray-700 mb-1">Traitement :</label>
                <textarea name="traitement" id="traitement" rows="3" required class="border p-2 rounded w-full"></textarea>
            </div>

            <div class="col-span-2">
                <label for="commentaire" class="block text-gray-700 mb-1">Commentaire :</label>
                <textarea name="commentaire" id="commentaire" rows="2" class="border p-2 rounded w-full"></textarea>
            </div>

            <div class="col-span-2 flex justify-between mt-4">
                <a href="dashboard.php?page=dossiers" class="text-blue-600 hover:underline">← Retour</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Ajouter</button>
            </div>
        </form>
    </div>
</body>
</html>
