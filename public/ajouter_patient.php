<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

require_once __DIR__ . '/../src/Patient.php';
$patientModel = new Patient();

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $telephone = $_POST['telephone'] ?? '';
    $sexe = $_POST['sexe'] ?? '';
    $email = $_POST['email'] ?? '';
    $numero_ss = $_POST['numero_ss'] ?? '';
    $mutuelle = $_POST['mutuelle'] ?? '';
    $contact_nom = $_POST['contact_nom'] ?? '';
    $contact_tel = $_POST['contact_tel'] ?? '';
    $antecedents = $_POST['antecedents'] ?? '';

    // Insertion
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("INSERT INTO patients (nom, prenom, sexe, date_naissance, adresse, telephone, email, numero_securite_sociale, mutuelle, personne_contact_nom, personne_contact_tel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $ok = $stmt->execute([
        $nom,
        $prenom,
        $sexe,
        $date_naissance,
        $patientModel->encryptData($adresse),
        $patientModel->encryptData($telephone),
        $patientModel->encryptData($email),
        $patientModel->encryptData($numero_ss),
        $patientModel->encryptData($mutuelle),
        $patientModel->encryptData($contact_nom),
        $patientModel->encryptData($contact_tel),
    ]);

    if ($ok) {
          header('Location: dashboard.php?page=patients');
        exit;
    } else {
        $message = "Erreur lors de l'ajout du patient.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Patient</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4 text-blue-700">Ajouter un nouveau patient</h2>

        <?php if ($message): ?>
            <p class="text-red-600 mb-4"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="post" class="grid grid-cols-2 gap-4">
            <input type="text" name="nom" placeholder="Nom" required class="border p-2 rounded">
            <input type="text" name="prenom" placeholder="Prénom" required class="border p-2 rounded">
            <select name="sexe" required class="border p-2 rounded">
                <option value="">Sélectionnez le sexe</option>
                <option value="Homme">Homme</option>
                <option value="Femme">Femme</option>
                <option value="Autre">Autre</option>
            </select>
            <input type="date" name="date_naissance" required class="border p-2 rounded col-span-2">
            <input type="text" name="adresse" placeholder="Adresse" required class="border p-2 rounded col-span-2">
            <input type="text" name="telephone" placeholder="Téléphone" required class="border p-2 rounded">
            <input type="email" name="email" placeholder="Email" required class="border p-2 rounded">
            <input type="text" name="numero_ss" placeholder="N° Sécurité Sociale" required class="border p-2 rounded col-span-2">
            <input type="text" name="mutuelle" placeholder="Mutuelle" class="border p-2 rounded col-span-2">
            <input type="text" name="contact_nom" placeholder="Nom personne à contacter" class="border p-2 rounded">
            <input type="text" name="contact_tel" placeholder="Téléphone contact" class="border p-2 rounded">

            <div class="col-span-2 flex justify-between mt-4">
                <a href="dashboard.php?page=patients" class="text-blue-600 hover:underline">← Retour</a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Ajouter</button>
            </div>
        </form>
    </div>
</body>
</html>                <?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}