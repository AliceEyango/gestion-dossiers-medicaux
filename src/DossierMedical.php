<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Patient.php';


class DossierMedical {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function getAllDossiers() {
        $sql = "SELECT dm.id, dm.date_consultation, dm.diagnostic, dm.traitement, dm.commentaire,
                       dm.created_at, p.nom, p.prenom
                FROM dossier_medical dm
                JOIN patients p ON dm.patient_id = p.id
                ORDER BY dm.date_consultation DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   



public function updateDossier($id, $date_consultation, $diagnostic, $traitement, $commentaire) {
    $sql = "UPDATE dossier_medical 
            SET date_consultation = :date_consultation,
                diagnostic = :diagnostic,
                traitement = :traitement,
                commentaire = :commentaire
            WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        'date_consultation' => $date_consultation,
        'diagnostic' => $diagnostic,
        'traitement' => $traitement,
        'commentaire' => $commentaire,
        'id' => $id
    ]);
}


   public function getDossierById($id) {
    $sql = "SELECT * FROM dossier_medical WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $id]);
    $dossier = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dossier) {
        $patient = new Patient();
        $dossier['diagnostic'] = $patient->decryptData($dossier['diagnostic']);
        $dossier['traitement'] = $patient->decryptData($dossier['traitement']);
        $dossier['commentaire'] = $patient->decryptData($dossier['commentaire']);
    }

    return $dossier;
}

public function deleteDossier($id) {
    $sql = "DELETE FROM dossier_medical WHERE id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute(['id' => $id]);
}

public function getDossierByIdRaw($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM dossier_medical WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



}
