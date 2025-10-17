<?php

// Démarrer la session AVANT tout affichage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//import des classes contrôleurs
require_once(__DIR__ . '/src/Controllers/RecetteController.php');
require_once(__DIR__ . '/src/Controllers/ContactController.php');
require_once(__DIR__ . '/src/Controllers/UserController.php');

// header
require_once __DIR__ . '/src/Views/header.php';

// routage double: c=contrôleur & a=action
$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';

// Initialisation des contrôleurs
$recetteController = new RecetteController();
$contactController = new ContactController();
$userController = new UserController();

switch ($controller) {
    case 'Recette':
    case 'recette':
        switch ($action) {
            case 'index':
            case 'lister':
                $recetteController->index();
                break;
            case 'ajouter':
                $recetteController->ajouter();
                break;
            case 'enregistrer':
                $recetteController->enregistrer();
                break;
            case 'detail':
                $recetteController->detail();
                break;
            case 'modifier':
                $recetteController->modifier();
                break;
            default:
                echo "Action non trouvée pour le contrôleur Recette";
        }   
        break;
        
    case 'contact':
        switch ($action) {
            case 'index':
                $contactController->index();
                break;
            case 'enregistrer':
                $contactController->enregistrer();
                break;
            default:
                $contactController->index();
        }
        break;
        
    case 'home':
        require_once __DIR__ . '/src/Controllers/homeController.php';
        break;
        
    // Rétrocompatibilité avec l'ancien système pour les contacts
    case 'enregistrer_contact':
        $contactController->enregistrer();
        break;
        
    // Rétrocompatibilité avec l'ancien système
    case 'ajout':
        $recetteController->ajouter();
        break;
    case 'enregistrer':
        $recetteController->enregistrer();
        break;
    case 'lister':
        $recetteController->index();
        break;
    case 'detail':
        $recetteController->detail();
        break;
        
    case 'User':
    case 'user':
        switch ($action) {
            case 'inscription':
                $userController->inscription();
                break;
            case 'enregistrer':
                $userController->enregistrer();
                break;
            case 'connexion':
                $userController->connexion();
                break;
            case 'verifieConnexion':
                $userController->verifieConnexion();
                break;
            case 'login':
                $userController->login();
                break;
            case 'deconnexion':
                $userController->deconnexion();
                break;
            case 'logout':
                $userController->logout();
                break;
            case 'profil':
                $userController->profil();
                break;
            default:
                echo "Action non trouvée pour le contrôleur User";
        }
        break;
        
    default:
        echo "Page non trouvée";
}

// footer
require_once __DIR__ . '/src/Views/footer.php';
