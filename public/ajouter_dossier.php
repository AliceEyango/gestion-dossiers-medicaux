<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/Patient.php';
require_once __DIR__ . '/../src/DossierMedical.php';


ini_set('display_errors', 1); 
error_reporting(E_ALL);       



$patientModel = new Patient();
$patients = $patientModel->getAllPatients();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $date_consultation = $_POST['date_consultation'];
    $diagnostic = $_POST['diagnostic'];
    $traitement = $_POST['traitement'];
    $commentaire = $_POST['commentaire'];

    $dossier = new DossierMedical();
    $dossier->ajouterDossier($patient_id, $date_consultation, $diagnostic, $traitement, $commentaire);

    header('Location: dashboard.php?page=dossiers');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un dossier médical</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

<div class="bg-white p-8 rounded-lg shadow-md w-full max-w-2xl">
    <h2 class="text-2xl font-bold text-blue-600 mb-6 text-center">
        <i class="fas fa-file-medical-alt mr-2"></i>Ajouter un dossier médical
    </h2>

    <form method="post" class="space-y-6">
        <!-- Patient -->
        <div>
            <label for="patient_id" class="block text-gray-700 font-medium mb-1">Patient :</label>
            <select id="patient_id" name="patient_id" required class="w-full border border-gray-300 rounded p-2 shadow-sm">
                <option value="">-- Sélectionner un patient --</option>
                <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Date de consultation -->
        <div>
            <label for="date_consultation" class="block text-gray-700 font-medium mb-1">Date de consultation :</label>
            <input type="date" id="date_consultation" name="date_consultation" required class="w-full border border-gray-300 rounded p-2 shadow-sm">
        </div>

        <!-- Diagnostic -->
        <div>
            <label for="diagnostic" class="block text-gray-700 font-medium mb-1">Diagnostic :</label>
            <textarea id="diagnostic" name="diagnostic" rows="3" required class="w-full border border-gray-300 rounded p-2 shadow-sm"></textarea>
        </div>

        <!-- Traitement -->
        <div>
            <label for="traitement" class="block text-gray-700 font-medium mb-1">Traitement :</label>
            <textarea id="traitement" name="traitement" rows="3" required class="w-full border border-gray-300 rounded p-2 shadow-sm"></textarea>
        </div>

        <!-- Commentaire -->
        <div>
            <label for="commentaire" class="block text-gray-700 font-medium mb-1">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" rows="2" class="w-full border border-gray-300 rounded p-2 shadow-sm"></textarea>
        </div>

        <!-- Boutons -->
        <div class="flex justify-between mt-6">
            <a href="dashboard.php?page=dossiers" class="text-gray-600 hover:text-blue-600 flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">
                <i class="fas fa-save mr-2"></i>Enregistrer
            </button>
        </div>
    </form>
</div>

</body>
</html>
