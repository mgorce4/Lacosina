<?php

namespace App\Models;

use PDO;

Class User {
    private $conn;

    //constructeur
    function __construct(){
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Récupérer tous les utilisateurs
    public function findAll(){
        $query = "SELECT * FROM users";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par son ID
    public function find($id){
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par son identifiant (login)
    public function findByIdentifiant($identifiant){
        $query = "SELECT * FROM users WHERE identifiant = :identifiant";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer un utilisateur par son email
    public function findByEmail($mail){
        $query = "SELECT * FROM users WHERE mail = :mail";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mail', $mail);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Recherche générique avec paramètres
    public function findBy(array $params){
        $query = "SELECT * FROM users WHERE ". implode(' AND ', array_map(function($key){
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);
        foreach ($params as $key => $value){
            $stmt->bindValue(":$key", $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ajouter un nouvel utilisateur
    public function add($identifiant, $password, $mail, $isAdmin = 0){
        // Hasher le mot de passe pour la sécurité
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (identifiant, password, mail, isAdmin, create_time) VALUES (:identifiant, :password, :mail, :isAdmin, NOW())";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':isAdmin', $isAdmin);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Mettre à jour un utilisateur
    public function update($id, $identifiant, $mail, $isAdmin){
        $query = "UPDATE users SET identifiant = :identifiant, mail = :mail, isAdmin = :isAdmin WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':isAdmin', $isAdmin);
        return $stmt->execute();
    }

    // Mettre à jour le profil d'un utilisateur (sans modifier isAdmin)
    public function updateProfil($id, $identifiant, $mail){
        $query = "UPDATE users SET identifiant = :identifiant, mail = :mail WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':identifiant', $identifiant);
        $stmt->bindParam(':mail', $mail);
        return $stmt->execute();
    }

    // Mettre à jour le mot de passe d'un utilisateur
    public function updatePassword($id, $newPassword){
        // Hasher le nouveau mot de passe
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $query = "UPDATE users SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':password', $hashedPassword);
        return $stmt->execute();
    }

    // Supprimer un utilisateur
    public function delete($id){
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // Vérifier les identifiants de connexion
    public function authenticate($identifiant, $password){
        $user = $this->findByIdentifiant($identifiant);
        
        if ($user && password_verify($password, $user['password'])){
            // Connexion réussie, retourner l'utilisateur sans le mot de passe
            unset($user['password']);
            return $user;
        }
        
        return false; // Identifiants incorrects
    }

    // Vérifier si un utilisateur est admin
    public function isAdmin($id){
        $user = $this->find($id);
        return $user && $user['isAdmin'] == 1;
    }
}