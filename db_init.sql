
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS medical_db;
USE medical_db;

-- Table des patients
CREATE TABLE patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    numero_securite_sociale VARCHAR(15) NOT NULL UNIQUE,
    adresse VARCHAR(255),
    telephone VARCHAR(20),
    email VARCHAR(100),
    personne_contact_nom VARCHAR(100),
    personne_contact_tel VARCHAR(20),
    mutuelle VARCHAR(100),
    antecedents_medicaux VARCHAR(255),
    date_enregistrement TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO patients 
(nom, prenom, date_naissance, numero_securite_sociale, adresse, telephone, email, personne_contact_nom, personne_contact_tel, mutuelle, antecedents_medicaux)
VALUES
('Dupont', 'Marie', '1990-05-12', '123456789012345', '123 Rue des Lilas, Paris', '0601020304', 'marie.dupont@email.com', 'Jean Dupont', '0605060708', 'Mutuelle SantéPlus', 'Asthme, Allergie au pollen'),
('Martin', 'Luc', '1982-11-30', '987654321098765', '45 Boulevard Haussmann, Paris', '0708091011', 'luc.martin@email.com', 'Claire Martin', '0611121314', 'AssurSanté', 'Hypertension');


