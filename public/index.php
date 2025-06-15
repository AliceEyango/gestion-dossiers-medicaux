<?php
session_start();

if (isset($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === 'admin' && $password === 'adminpass') {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = 'admin';
        header('Location: dashboard.php');
        exit;
    } elseif ($username === 'user' && $password === 'userpass') {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = 'user';
        header('Location: dashboard.php');
        exit;
    } else {
        $message = "Identifiants incorrects";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - Gestion Patients</title>
    <link rel="stylesheet" href="../css/index.css">
</head>
<body>
    <div class="overlay"></div>

    <div class="login-container">
        <div class="login-form">
            <h2>Connexion à l'Espace Patient</h2>

            <?php if ($message): ?>
                <div class="error"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="text" name="username" placeholder="Nom d'utilisateur" required>
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit">Se connecter</button>
            </form>

            <div class="footer">© 2025 Gestion des Dossiers Médicaux</div>
        </div>
    </div>
</body>
</html>
