<?php
session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

// Vérifier que l'ID est passé dans l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('ID patient invalide.');
}

$id = (int)$_GET['id'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cle = $_POST['cle'] ?? '';

    if ($cle === 'cette_cle_de_32_bytes_pour_AES256_!@#') {
        $_SESSION['cle_dechiffrement'] = $cle;
        $_SESSION['message_cle'] = "Clé correcte";
        header("Location: modifier_dossier.php?id=$id");
        exit;
    } else {
        $message = "Clé incorrecte.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Clé de déchiffrement</title>
</head>
<body>
    <h2>Entrez la clé de déchiffrement pour pouvoir modifier le dossier du patient</h2>
    <?php if ($message): ?>
        <p style="color:red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <label>Clé :</label>
        <input type="password" name="cle" required>
        <button type="submit">Valider</button>
    </form>
