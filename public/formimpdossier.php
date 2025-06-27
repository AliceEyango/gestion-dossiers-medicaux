<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
// Vérifier que l'ID du patient est fourni dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID invalide');
}

$id = (int)$_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cle = $_POST['cle'] ?? '';
    $idPost = $_POST['id'] ?? '';

    if (!is_numeric($idPost)) {
        die('ID invalide (POST)');
    }

    
    $_SESSION['cle_dechiffrement'] = $cle;

    // Rediriger vers le script d'impression avec l'ID
    header("Location: imprimer_dossier.php?id=" . (int)$idPost);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clé de déchiffrement</title>
</head>
<body>
    <h2>Entrez la clé de déchiffrement pour imprimer le dossier du patient</h2>
    <p> Si vous n' avez pas la bonne clé les données seront chiffrées.</p>

    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <label for="cle">Clé :</label>
        <input type="password" name="cle" id="cle" required>

        <button type="submit">Imprimer</button>
    </form>
</body>
</html>