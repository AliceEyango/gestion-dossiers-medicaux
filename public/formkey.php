<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['cle_dechiffrement'] = $_POST['cle'] ?? '';
    header('Location: imprimer_liste_patients.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Clé de déchiffrement</title></head>
<body>
    <h2>Entrez la clé de déchiffrement pour imprimer la liste des patients</h2>
    <p> Si vous n' avez pas la bonne clé les données seront chiffrées.</p>
    <form method="POST">
        <label>Clé :</label>
        <input type="password" name="cle" required>
        <button type="submit">Imprimer</button>
    </form>
</body>
</html>
