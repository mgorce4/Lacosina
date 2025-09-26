<?php

    //récupération des données de formulaire
    $titre = $_POST['titre'];
    $description = $_POST['description'];
    $auteur = $_POST['auteur'];

    //préparation de la requête d'insertio dans la base de données

    /** @var PDO $pdo **/

    $requete = $pdo->prepare('INSERT INTO recettes (titre, description, auteur, date_creation) VALUES (:titre, :description, :auteur, NOW())');
    $requete->bindParam(':titre', $titre);
    $requete->bindParam(':description', $description);
    $requete->bindParam(':auteur', $auteur);

    //exécution de la requête
    $ajoutOk = $requete->execute();

    if ($ajoutOk){
        require_once(__DIR__ . '/../Views/enregistrer.php');
    } else {
        echo 'Erreur lors de l\'enregistrement de la recette.';
    }