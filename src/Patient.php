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
    if (empty($data)) return '';
    
    $decoded = base64_decode($data, true);
    if ($decoded === false || strlen($decoded) < 17) {
        return 'Donnee corrompue';
    }

    $iv = substr($decoded, 0, 16);
    $encrypted = substr($decoded, 16);

    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    return $decrypted !== false ? $decrypted : 'Erreur déchiffrement';
}


  public function getAllPatients() {
    $stmt = $this->pdo->query("
        SELECT 
            id, nom, prenom, sexe, date_naissance,
            numero_securite_sociale, adresse, telephone,
            email, personne_contact_nom, personne_contact_tel,
            mutuelle
        FROM patients
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function supprimerPatient($id) {
    $stmt = $this->pdo->prepare("DELETE FROM patients WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}


public function getPatientById($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($patient) {
        $patient['adresse'] = $this->decryptData($patient['adresse']);
        $patient['telephone'] = $this->decryptData($patient['telephone']);
        $patient['email'] = $this->decryptData($patient['email']);
        $patient['numero_securite_sociale'] = $this->decryptData($patient['numero_securite_sociale']);
        $patient['mutuelle'] = $this->decryptData($patient['mutuelle']);
        $patient['personne_contact_nom'] = $this->decryptData($patient['personne_contact_nom']);
        $patient['personne_contact_tel'] = $this->decryptData($patient['personne_contact_tel']);
    }

    return $patient;
}

public function getPatientByIdRaw($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM patients WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



// Chiffrement avec GPG
public function gpgEncrypt($data, $publicKeyPath) {
    putenv("GNUPGHOME=/tmp");
    $gpg = new gnupg();
    $import = $gpg->import(file_get_contents($publicKeyPath));
    if (!$import || empty($import['fingerprint'])) return "Erreur import clé publique.";
    $gpg->addencryptkey($import['fingerprint']);
    return $gpg->encrypt($data);
}

public function gpgDecrypt($data, $privateKeyPath, $passphrase) {
    putenv("GNUPGHOME=/tmp");
    $gpg = new gnupg();
    $import = $gpg->import(file_get_contents($privateKeyPath));
    if (!$import || empty($import['fingerprint'])) return "Erreur import clé privée.";
    $gpg->adddecryptkey($import['fingerprint'], $passphrase);
    return $gpg->decrypt($data);
}



}
