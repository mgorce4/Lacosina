<?php

namespace App\Models;

use PDO;

Class Recette {
    private $conn;

    //constructeur
    function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    public function findAll(){
        $query = "SELECT * FROM recettes ORDER BY FIELD(type_plat, 'entree', 'plat', 'dessert'), titre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id){
        $query = "SELECT * FROM recettes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBy(array $params){
        $query = "SELECT * FROM recettes WHERE ". implode(' AND ', array_map(function($key){
            return "$key = :$key";
        }, array_keys($params))) . " ORDER BY FIELD(type_plat, 'entree', 'plat', 'dessert'), titre ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value){
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($titre, $description, $auteur, $image, $type_plat = 'entree', $isApproved = 0){
        $query = "INSERT INTO recettes (titre, description, auteur, image, type_plat, isApproved, date_creation) VALUES (:titre, :description, :auteur, :image, :type_plat, :isApproved, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':auteur', $auteur);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':type_plat', $type_plat);
        $stmt->bindParam(':isApproved', $isApproved, PDO::PARAM_INT);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function update($id, $titre, $description, $auteur, $image, $type_plat){
        $query = "UPDATE recettes SET titre = :titre, description = :description, auteur = :auteur, image = :image, type_plat = :type_plat WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':auteur', $auteur);
        $stmt->bindParam(':image', $image);
        $stmt->bindParam(':type_plat', $type_plat);
        return $stmt->execute();
    }

    public function delete($id){
        $query = "DELETE FROM recettes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function findAllApproved(){
        $query = "SELECT * FROM recettes WHERE isApproved = 1 ORDER BY FIELD(type_plat, 'entree', 'plat', 'dessert'), titre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findAllPending(){
        $query = "SELECT * FROM recettes WHERE isApproved = 0 ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function approve($id){
        $query = "UPDATE recettes SET isApproved = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function findPendingByAuthor($auteur){
        $query = "SELECT * FROM recettes WHERE isApproved = 0 AND auteur = :auteur ORDER BY date_creation DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':auteur', $auteur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPending(){
        $query = "SELECT COUNT(*) as count FROM recettes WHERE isApproved = 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }
}
