<?php

require_once __DIR__ . '/Database.php';

Class Favori {
    private $conn;

    //constructeur
    function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer tous les favoris
    public function findAll(){
        $query = "SELECT * FROM favoris";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un favori par son ID
    public function find($id){
        $query = "SELECT * FROM favoris WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les favoris d'un utilisateur
    public function findByUserId($userId){
        $query = "SELECT f.*, r.titre, r.description, r.auteur, r.image, r.date_creation 
                  FROM favoris f
                  INNER JOIN recettes r ON f.recette_id = r.id
                  WHERE f.user_id = :user_id
                  ORDER BY f.create_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer tous les utilisateurs qui ont mis une recette en favori
    public function findByRecetteId($recetteId){
        $query = "SELECT f.*, u.identifiant, u.mail 
                  FROM favoris f
                  INNER JOIN users u ON f.user_id = u.id
                  WHERE f.recette_id = :recette_id
                  ORDER BY f.create_time DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Vérifier si une recette est en favori pour un utilisateur
    public function isFavorite($userId, $recetteId){
        $query = "SELECT * FROM favoris WHERE user_id = :user_id AND recette_id = :recette_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result !== false && $result !== null;
    }

    // Recherche générique avec paramètres
    public function findBy(array $params){
        $query = "SELECT * FROM favoris WHERE ". implode(' AND ', array_map(function($key){
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value){
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un favori
    public function add($recetteId, $userId){
        try {
            $query = "INSERT INTO favoris (recette_id, user_id, create_time) VALUES (:recette_id, :user_id, NOW())";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':recette_id', $recetteId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            // Si c'est une erreur de doublon (code 23000), retourner false
            if ($e->getCode() == 23000) {
                return false;
            }
            // Sinon, relancer l'exception
            throw $e;
        }
    }

    // Supprimer un favori par ID
    public function delete($id){
        $query = "DELETE FROM favoris WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Supprimer un favori par recette_id et user_id
    public function deleteByRecetteAndUser($recetteId, $userId){
        $query = "DELETE FROM favoris WHERE recette_id = :recette_id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Compter le nombre de favoris pour une recette
    public function countByRecette($recetteId){
        $query = "SELECT COUNT(*) as total FROM favoris WHERE recette_id = :recette_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':recette_id', $recetteId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }

    // Compter le nombre de favoris pour un utilisateur
    public function countByUser($userId){
        $query = "SELECT COUNT(*) as total FROM favoris WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
}
