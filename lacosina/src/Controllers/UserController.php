<?php

namespace App\Controllers;

use App\Models\User;

class UserController{
    private $userModel;

    public function __construct(){
        $this->userModel = new User();
    }

    //fonction permettant d'afficher le formulaire d'inscription
    function inscription(){
        require_once(__DIR__ . '/../Views/User/inscription.php');
    }

    //fonction permettant d'enregistrer un nouvel utilisateur
    function enregistrer(){
        //récupération des données du formulaire
        $identifiant = $_POST['identifiant'];
        $password = $_POST['password'];
        $mail = $_POST['mail'];
        
        // Vérifier si l'identifiant existe déjà
        $userExist = $this->userModel->findByIdentifiant($identifiant);
        if ($userExist) {
            echo '<div class="alert alert-danger">Cet identifiant existe déjà. Veuillez en choisir un autre.</div>';
            require_once(__DIR__ . '/../Views/User/inscription.php');
            return;
        }
        
        // Vérifier si l'email existe déjà
        $emailExist = $this->userModel->findByEmail($mail);
        if ($emailExist) {
            echo '<div class="alert alert-danger">Cet email est déjà utilisé.</div>';
            require_once(__DIR__ . '/../Views/User/inscription.php');
            return;
        }
        
        // Encoder le mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Par défaut, l'utilisateur n'est pas admin
        $isAdmin = 0;
        
        // Utilisation du modèle pour enregistrer l'utilisateur
        $resultat = $this->userModel->add($identifiant, $password, $mail, $isAdmin);

        if ($resultat){
            require_once(__DIR__ . '/../Views/User/enregistrement.php');
        } else {
            echo '<div class="alert alert-danger">Erreur lors de l\'enregistrement de l\'utilisateur.</div>';
        }
    }

    //fonction permettant d'afficher le formulaire de connexion
    function connexion(){
        require_once(__DIR__ . '/../Views/User/connexion.php');
    }

    //fonction permettant de vérifier la connexion d'un utilisateur
    function verifieConnexion(){
        // Récupération des données du formulaire
        $identifiant = $_POST['identifiant'];
        $password = $_POST['password'];
        
        // Requête de vérification de l'identifiant
        $user = $this->userModel->findByIdentifiant($identifiant);
        
        // Vérification de l'existence de l'utilisateur et du mot de passe avec password_verify
        if ($user && password_verify($password, $user['password'])) {
            // Identifiants corrects : enregistrer dans la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['identifiant'] = $user['identifiant'];
            $_SESSION['mail'] = $user['mail'];
            $_SESSION['isAdmin'] = $user['isAdmin'];
            
            // Redirection vers la page d'accueil
            echo '<div class="alert alert-success">Connexion réussie ! Bienvenue ' . htmlspecialchars($user['identifiant']) . '</div>';
            echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 2000);</script>';
        } else {
            // Identifiants incorrects : afficher message d'erreur
            echo '<div class="alert alert-danger">Identifiant ou mot de passe incorrect.</div>';
            require_once(__DIR__ . '/../Views/User/connexion.php');
        }
    }

    //fonction permettant de connecter un utilisateur (ancienne version, garde pour compatibilité)
    function login(){
        // Rediriger vers verifieConnexion
        $this->verifieConnexion();
    }

    //fonction permettant de déconnecter un utilisateur
    function deconnexion(){
        // Supprimer la session à l'aide de session_destroy()
        session_unset();
        session_destroy();
        
        // Redirection vers l'accueil
        echo '<div class="alert alert-info">Vous avez été déconnecté.</div>';
        echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 2000);</script>';
    }

    //fonction permettant de déconnecter un utilisateur (ancienne version, garde pour compatibilité)
    function logout(){
        // Rediriger vers deconnexion
        $this->deconnexion();
    }

    //fonction permettant d'afficher le profil de l'utilisateur
    function profil(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-warning">Vous devez être connecté pour accéder à votre profil.</div>';
            echo '<a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>';
            return;
        }
        
        require_once(__DIR__ . '/../Views/User/profil.php');
    }

    //fonction permettant de modifier le profil de l'utilisateur
    function modifierProfil(){
        // Définir le header JSON
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour modifier votre profil.']);
            exit; // Important : arrêter l'exécution pour éviter le header/footer
        }

        // Vérifier que la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
            exit;
        }

        // Récupérer les données JSON envoyées
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            // Si ce n'est pas du JSON, essayer avec $_POST
            $data = $_POST;
        }

        $userId = $_SESSION['user_id'];
        $newIdentifiant = trim($data['identifiant'] ?? '');
        $newMail = trim($data['mail'] ?? '');

        // Validation des données
        if (empty($newIdentifiant) || empty($newMail)) {
            echo json_encode(['success' => false, 'message' => 'L\'identifiant et l\'email ne peuvent pas être vides.']);
            exit;
        }

        // Validation du format email
        if (!filter_var($newMail, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'L\'adresse email n\'est pas valide.']);
            exit;
        }

        // Vérifier si le nouvel identifiant est déjà utilisé par un autre utilisateur
        $existingUser = $this->userModel->findByIdentifiant($newIdentifiant);
        if ($existingUser && $existingUser['id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Cet identifiant est déjà utilisé par un autre utilisateur.']);
            exit;
        }

        // Vérifier si le nouvel email est déjà utilisé par un autre utilisateur
        $existingEmail = $this->userModel->findByEmail($newMail);
        if ($existingEmail && $existingEmail['id'] != $userId) {
            echo json_encode(['success' => false, 'message' => 'Cet email est déjà utilisé par un autre utilisateur.']);
            exit;
        }

        // Mettre à jour le profil
        $result = $this->userModel->updateProfil($userId, $newIdentifiant, $newMail);

        if ($result) {
            // Mettre à jour la session
            $_SESSION['identifiant'] = $newIdentifiant;
            $_SESSION['mail'] = $newMail;
            
            echo json_encode([
                'success' => true, 
                'message' => 'Profil mis à jour avec succès.',
                'identifiant' => $newIdentifiant,
                'mail' => $newMail
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du profil.']);
        }
        exit; // Important : arrêter l'exécution pour éviter le header/footer
    }
}