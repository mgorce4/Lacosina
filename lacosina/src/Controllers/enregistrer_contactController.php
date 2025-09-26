<?php

    //récupération des données de formulaire 
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $description = $_POST['description'];

    //préparation de la requête d'insertion dans la base de données

    /** @var PDO $pdo **/
    $requete = $pdo->prepare('INSERT INTO contacts (nom, email, description, date_envoi) VALUES (:nom, :email, :description, NOW())');
    $requete->bindParam(':nom', $nom);
    $requete->bindParam(':email', $email);
    $requete->bindParam(':description', $description);

    //exécution de la requête
    $ajoutOk = $requete->execute();

    if ($ajoutOk){
        require_once(__DIR__ . '/../Views/enregistrer_contact.php');
    } else {
        echo 'Erreur lors de l\'enregistrement du contact.';
    }