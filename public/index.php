<?php
session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../src/Database.php';


$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    try {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT username, password FROM connexion WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = 'admin';
            header('Location: dashboard.php');
            exit;
        } else {
            $message = "Identifiants incorrects";
        }
    } catch (PDOException $e) {
        $message = "Erreur serveur, veuillez réessayer plus tard.";
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
