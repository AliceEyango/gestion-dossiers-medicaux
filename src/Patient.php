<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/../config/config.php';

class Patient {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function encryptData($data) {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decryptData($data) {
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    }

   public function getAllPatients() {
    $stmt = $this->pdo->query("
        SELECT 
            nom, prenom, date_naissance,
            numero_securite_sociale, adresse, telephone,
            email, personne_contact_nom, personne_contact_tel,
            mutuelle, antecedents_medicaux
        FROM patients
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
