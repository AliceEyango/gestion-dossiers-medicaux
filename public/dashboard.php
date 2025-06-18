<?php
ini_set('display_errors', 0);
error_reporting(E_ALL);
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../src/Patient.php';

$patientModel = new Patient();
$patients = $patientModel->getAllPatients();
$page = $_GET['page'] ?? 'accueil';
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="flex bg-gray-100 min-h-screen">

<!-- Menu latéral -->
<aside class="w-64 bg-white shadow p-6 flex flex-col min-h-screen">
    <h1 class="text-2xl font-bold text-blue-600 mb-6">Cabinet Médical</h1>
    <nav class="flex flex-col flex-1 space-y-4">
        <a href="?page=accueil" class="flex items-center gap-2 <?= $page === 'accueil' ? 'text-blue-700 font-bold' : 'text-gray-700 hover:text-blue-600' ?>"><i class="fas fa-home"></i> Accueil</a>
        <a href="?page=patients" class="flex items-center gap-2 <?= $page === 'patients' ? 'text-blue-700 font-bold' : 'text-gray-700 hover:text-blue-600' ?>"><i class="fas fa-user-injured"></i> Patients</a>
        
         <div class="flex-grow"></div>

        <a href="logout.php" class="mt-auto bg-red-500 hover:bg-red-600 text-white text-center py-2 rounded"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </nav>
</aside>

<!-- Contenu principal -->
<main class="flex-1 p-6">

<?php if ($page === 'accueil'): ?>

    <!-- ACCUEIL -->
   <div class="bg-white rounded-xl shadow-xl p-12 max-w-6xl flex flex-col md:flex-row items-center gap-12 mx-auto">
    <img src="/gestion-dossiers-medicaux/assets/patient-login.png" alt="Docteur et patient" class="w-full md:w-2/3 rounded-lg shadow-md">
    
    <div class="flex flex-col items-center text-center space-y-8 md:w-1/3">
        <h1 class="text-4xl font-bold text-blue-700 leading-tight">
            Bienvenue dans votre application<br><span class="text-4xl font-bold text-blue-700 leading-tight">de gestion des patients</span>
        </h1>
        <p class="text-lg text-gray-600">
            Accédez à votre tableau de bord pour gerer les dossiers medicaux de vos patients.
        </p>
        <a href="?page=patients" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg shadow-md transition duration-300 ease-in-out text-xl">
            Accéder au tableau de bord
            <i class="fas fa-arrow-right ml-3"></i>
        </a>
    </div>
</div>


<?php elseif ($page === 'patients'): ?>

    <!-- PATIENTS -->
   <div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-semibold">Liste des patients</h2>
    <div class="space-x-2">
        <a href="ajouter_patient.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow inline-block">
            <i class="fas fa-plus mr-2"></i>Ajouter un patient
        </a>
        <a href="formkey.php" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow inline-block">
            <i class="fas fa-print mr-2"></i>Imprimer la liste
        </a>
    </div>
</div>


    <input type="text" id="search" placeholder="Rechercher par nom ou prénom..." class="w-full p-2 mb-4 border rounded shadow-sm" style="width: 15%;">

    <?php if (count($patients) === 0): ?>
        <p class="text-red-500">Aucun patient trouvé.</p>
    <?php else: ?>
    <div class="overflow-x-auto bg-white rounded shadow">
        <table class="w-full text-sm text-left table-auto">
            <thead class="bg-blue-100 text-gray-800">
                <tr>
                    <th class="p-2">Nom</th>
                    <th class="p-2">Prénom</th>
                    <th class="p-2">Date naissance</th>
                    <th class="p-2">Adresse</th>
                    <th class="p-2">Téléphone</th>
                    <th class="p-2">Mutuelle</th>
                    <th class="p-2 text-center" style="min-width: 150px; max-width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody id="patientTable">
                <?php foreach ($patients as $p): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-2"><?= htmlspecialchars($p['nom']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['prenom']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['date_naissance']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['adresse']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['telephone']) ?></td>
                    <td class="p-2"><?= htmlspecialchars($p['mutuelle']) ?></td>
                    <td class="p-2 text-center space-x-2" style="min-width: 150px; max-width: 150px;">
                        <a href="formmodif.php?id=<?= $p['id'] ?>" class="text-yellow-500 hover:text-yellow-600"><i class="fas fa-edit"></i></a>
                        <a href="supprimer_patient.php?id=<?= $p['id'] ?>" class="text-red-500 hover:text-red-600" onclick="return confirm('Voulez-vous vraiment Supprimer ce patient ?');"><i class="fas fa-trash-alt"></i></a>
                        <a href="formkey1.php?id=<?= $p['id'] ?>" class="text-blue-500 hover:text-blue-600"><i class="fas fa-file-pdf"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

<?php else: ?>
    <p class="text-red-500">Page introuvable.</p>
<?php endif; ?>

</main>

<!-- JS pour recherche -->
<script>
document.getElementById('search')?.addEventListener('input', function () {
    const search = this.value.toLowerCase();
    const rows = document.querySelectorAll('#patientTable tr');

    rows.forEach(row => {
        const nom = row.children[0].textContent.toLowerCase();
        const prenom = row.children[1].textContent.toLowerCase();
        row.style.display = (nom.includes(search) || prenom.includes(search)) ? '' : 'none';
    });
});
</script>
</body>
</html>
