<?php

namespace App\Models;

use PDO;

Class Commentaire {
    private $conn;

    //constructeur
    function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer tous les commentaires
    public function findAll(){
        $query = "SELECT * FROM comments ORDER BY create_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un commentaire par son ID
    public function find($id){
        $query = "SELECT * FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les commentaires d'une recette
    public function findByRecetteId($recetteId){
        $query = "SELECT * FROM comments WHERE recette_id = :recette_id AND isApproved = 1 ORDER BY create_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recherche générique avec paramètres
    public function findBy(array $params){
        $query = "SELECT * FROM comments WHERE ". implode(' AND ', array_map(function($key){
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value){
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un nouveau commentaire
    public function add($recetteId, $pseudo, $commentaire, $isApproved = 0){
        $query = "INSERT INTO comments (recette_id, pseudo, commentaire, isApproved, create_time) VALUES (:recette_id, :pseudo, :commentaire, :isApproved, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':commentaire', $commentaire);
        $stmt->bindParam(':isApproved', $isApproved, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Mettre à jour un commentaire
    public function update($id, $pseudo, $commentaire){
        $query = "UPDATE comments SET pseudo = :pseudo, commentaire = :commentaire WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':pseudo', $pseudo);
        $stmt->bindParam(':commentaire', $commentaire);
        return $stmt->execute();
    }

    // Supprimer un commentaire
    public function delete($id){
        $query = "DELETE FROM comments WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Compter le nombre de commentaires pour une recette
    public function countByRecette($recetteId){
        $query = "SELECT COUNT(*) as total FROM comments WHERE recette_id = :recette_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Récupérer tous les commentaires en attente
    public function findAllPending(){
        $query = "SELECT c.*, r.titre as recette_titre FROM comments c LEFT JOIN recettes r ON c.recette_id = r.id WHERE c.isApproved = 0 ORDER BY c.create_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approuver un commentaire
    public function approve($id){
        $query = "UPDATE comments SET isApproved = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Compter les commentaires en attente
    public function countPending(){
        $query = "SELECT COUNT(*) as count FROM comments WHERE isApproved = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}

