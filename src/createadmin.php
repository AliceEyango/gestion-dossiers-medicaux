<?php
require_once __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $username = 'admin';
    $password = 'adminpass';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO connexion (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $passwordHash]);

    echo "Utilisateur admin créé avec succès.";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
