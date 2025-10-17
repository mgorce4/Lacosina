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
}