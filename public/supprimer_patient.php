<?php
require_once __DIR__ . '/../src/Patient.php';
$patientModel = new Patient();

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $patientModel->supprimerPatient($id);
}

  header('Location: dashboard.php?page=patients');
exit;
