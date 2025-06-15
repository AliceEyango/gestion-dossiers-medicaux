<?php
// Afficher les erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Démarrer la session
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Inclure la classe Patient
require_once __DIR__ . '/../src/Patient.php';

// Instancier le modèle Patient
$patientModel = new Patient();
$patients = $patientModel->getAllPatients();

// Vérifier le rôle utilisateur
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/dashboard.css">

</head>
<body>
    <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user']) ?> (<?= $isAdmin ? 'Admin' : 'Utilisateur' ?>)</h1>
    <a href="logout.php">Déconnexion</a>

    <h2>Liste des patients</h2>

    <?php if (count($patients) === 0): ?>
        <p>Aucun patient trouvé.</p>
    <?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date naissance</th>
                <th>N° SS</th>
                <th>Adresse</th>
                <th>Téléphone</th>
                <th>Email</th>
                <th>Personne à contacter</th>
                <th>Mutuelle</th>
                <th>Antécédents</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['nom']) ?></td>
                <td><?= htmlspecialchars($p['prenom']) ?></td>
                <td><?= htmlspecialchars($p['date_naissance']) ?></td>
                <td><?= htmlspecialchars($patientModel->decryptData($p['numero_securite_sociale'])) ?></td>
                <td><?= htmlspecialchars($patientModel->decryptData($p['adresse'])) ?></td>
                <td><?= htmlspecialchars($patientModel->decryptData($p['telephone'])) ?></td>
                <td><?= htmlspecialchars($patientModel->decryptData($p['email'])) ?></td>
                <td>
                    <?= htmlspecialchars($patientModel->decryptData($p['personne_contact_nom'])) ?>
                    (<?= htmlspecialchars($patientModel->decryptData($p['personne_contact_tel'])) ?>)
                </td>
                <td><?= htmlspecialchars($patientModel->decryptData($p['mutuelle'])) ?></td>
                <td><?= htmlspecialchars($patientModel->decryptData($p['antecedents_medicaux'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</body>
</html>
