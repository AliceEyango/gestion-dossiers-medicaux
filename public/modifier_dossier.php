<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ini_set('display_errors', 1); 
error_reporting(E_ALL);  

require_once __DIR__ . '/../src/DossierMedical.php';

$dossierModel = new DossierMedical();

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID du dossier non fourni.";
    exit;
}

$dossier = $dossierModel->getDossierById($id);

if (!$dossier) {
    echo "Dossier introuvable.";
    exit;
}

// Si formulaire soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date_consultation = $_POST['date_consultation'] ?? '';
    $diagnostic = $_POST['diagnostic'] ?? '';
    $traitement = $_POST['traitement'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';

    $success = $dossierModel->updateDossier($id, $date_consultation, $diagnostic, $traitement, $commentaire);

    if ($success) {
        header('Location: dashboard.php?page=dossiers');

        exit;
    } else {
        $error = "Erreur lors de la mise à jour.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Modifier dossier médical</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">

<div class="max-w-xl mx-auto bg-white p-6 rounded shadow">

    <h1 class="text-2xl font-bold mb-6">Modifier le dossier médical</h1>

    <?php if (!empty($error)): ?>
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div>
            <label class="block font-semibold mb-1" for="date_consultation">Date de consultation :</label>
            <input type="date" id="date_consultation" name="date_consultation" required
                   value="<?= htmlspecialchars($dossier['date_consultation']) ?>"
                   class="w-full p-2 border rounded" />
        </div>

        <div>
            <label class="block font-semibold mb-1" for="diagnostic">Diagnostic :</label>
            <textarea id="diagnostic" name="diagnostic" required class="w-full p-2 border rounded"><?= htmlspecialchars($dossier['diagnostic']) ?></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1" for="traitement">Traitement :</label>
            <textarea id="traitement" name="traitement" required class="w-full p-2 border rounded"><?= htmlspecialchars($dossier['traitement']) ?></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1" for="commentaire">Commentaire :</label>
            <textarea id="commentaire" name="commentaire" class="w-full p-2 border rounded"><?= htmlspecialchars($dossier['commentaire']) ?></textarea>
        </div>

        <div class="flex justify-between items-center">
            <a href="dashboard.php?page=dossiers" class="text-blue-600 hover:underline">Retour à la liste</a>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Enregistrer</button>
        </div>
    </form>

</div>

</body>
</html>
