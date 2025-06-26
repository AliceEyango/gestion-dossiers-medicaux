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
        return 'Donnée corrompue';
    }

    $iv = substr($decoded, 0, 16);
    $encrypted = substr($decoded, 16);

    $decrypted = openssl_decrypt($encrypted, 'aes-256-cbc', ENCRYPTION_KEY, 0, $iv);
    return $decrypted !== false ? $decrypted : 'Erreur déchiffrement';
}


  public function getAllPatients() {
    $stmt = $this->pdo->query("
        SELECT 
            id, nom, prenom, date_naissance,
            numero_securite_sociale, adresse, telephone,
            email, personne_contact_nom, personne_contact_tel,
            mutuelle, antecedents_medicaux
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
        $patient['antecedents_medicaux'] = $this->decryptData($patient['antecedents_medicaux']);
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

 // ===== GPG Chiffrement =====
    public static function encryptGPG($data) {
        $input = tempnam(sys_get_temp_dir(), 'gpg_in_');
        $output = tempnam(sys_get_temp_dir(), 'gpg_out_');
        file_put_contents($input, $data);

        $cmd = sprintf(
            'gpg --yes --batch --trust-model always --output %s --encrypt --recipient-file %s %s',
            escapeshellarg($output),
            escapeshellarg(GPG_PUBLIC_KEY),
            escapeshellarg($input)
        );
        exec($cmd, $out, $status);
        if ($status !== 0) return false;

        $encrypted = file_get_contents($output);
        unlink($input); unlink($output);
        return base64_encode($encrypted);
    }

    public static function decryptGPG($data, $passphrase) {
    $decoded = base64_decode($data);
    $input = tempnam(sys_get_temp_dir(), 'gpg_in_');
    $output = tempnam(sys_get_temp_dir(), 'gpg_out_');
    $passfile = tempnam(sys_get_temp_dir(), 'gpg_pass_');

    file_put_contents($input, $decoded);
    file_put_contents($passfile, $passphrase);

    putenv("GNUPGHOME=/tmp/.gnupg");

    $cmd = sprintf(
        'gpg --batch --yes --passphrase-file %s --output %s --decrypt %s 2>&1',
        escapeshellarg($passfile),
        escapeshellarg($output),
        escapeshellarg($input)
    );

    exec($cmd, $outputLines, $status);

    unlink($input);
    unlink($passfile);

    if ($status !== 0) {
        return "⚠️ Erreur déchiffrement GPG\n" . implode("\n", $outputLines);
    }

    $decrypted = file_get_contents($output);
    unlink($output);
    return $decrypted ?: "⚠️ Donnée vide ou corrompue";
}


}
