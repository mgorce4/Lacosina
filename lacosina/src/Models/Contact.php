<?php

require_once __DIR__ . '/Database.php';

Class Contact {
    private $conn;

    //constructeur
    function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function findAll(){
        $query = "SELECT * FROM contacts ORDER BY date_envoi DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id){
        $query = "SELECT * FROM contacts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($nom, $email, $description){
        $query = "INSERT INTO contacts (nom, email, description, date_envoi) VALUES (:nom, :email, :description, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    public function delete($id){
        $query = "DELETE FROM contacts WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}