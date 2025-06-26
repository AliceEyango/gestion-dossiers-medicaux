<?php
require_once __DIR__ . '/../src/DossierMedical.php';
session_start();

if (!isset($_SESSION['user']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$dossierModel = new DossierMedical();
$dossierModel->deleteDossier($_GET['id']);

header('Location: dashboard.php?page=dossiers');
exit;
?>