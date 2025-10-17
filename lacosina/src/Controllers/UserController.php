<?php

//connexion à la base de données
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'User.php');

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

    //fonction permettant de connecter un utilisateur
    function login(){
        //récupération des données du formulaire
        $identifiant = $_POST['identifiant'];
        $password = $_POST['password'];
        
        // Authentification de l'utilisateur
        $user = $this->userModel->authenticate($identifiant, $password);
        
        if ($user) {
            // Stocker les informations de l'utilisateur en session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['identifiant'] = $user['identifiant'];
            $_SESSION['mail'] = $user['mail'];
            $_SESSION['isAdmin'] = $user['isAdmin'];
            
            // Redirection vers la page d'accueil
            echo '<div class="alert alert-success">Connexion réussie ! Bienvenue ' . htmlspecialchars($user['identifiant']) . '</div>';
            echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 2000);</script>';
        } else {
            echo '<div class="alert alert-danger">Identifiant ou mot de passe incorrect.</div>';
            require_once(__DIR__ . '/../Views/User/connexion.php');
        }
    }

    //fonction permettant de déconnecter un utilisateur
    function logout(){
        // Détruire toutes les variables de session
        session_unset();
        session_destroy();
        
        // Redirection vers la page d'accueil
        echo '<div class="alert alert-info">Vous avez été déconnecté.</div>';
        echo '<script>setTimeout(function(){ window.location.href = "index.php"; }, 2000);</script>';
    }
}