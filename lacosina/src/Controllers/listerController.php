<?php

    //préparation de la requête d'insertion dans la base de données

    /** @var PDO $pdo **/
    $requete = $pdo->prepare("SELECT * FROM recettes");

    //exécution de la requête et récupération des données
    $requete->execute();
    $recettes = $requete->fetchAll(PDO::FETCH_ASSOC);

    require_once(__DIR__ . '/../Views/liste.php');