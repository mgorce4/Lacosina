<?php

// Démarrer la session AVANT tout affichage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//import des classes contrôleurs
require_once(__DIR__ . '/src/Controllers/RecetteController.php');
require_once(__DIR__ . '/src/Controllers/ContactController.php');
require_once(__DIR__ . '/src/Controllers/UserController.php');
require_once(__DIR__ . '/src/Controllers/FavoriController.php');

// routage double: c=contrôleur & a=action
$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';

// Liste des actions qui retournent du JSON (sans header/footer)
$jsonActions = [
    'getFavoris',
    'modifierProfil',
    'ajouter',
    'supprimer',
    'toggle'
];

// Afficher le header seulement si ce n'est pas une action JSON
if (!in_array($action, $jsonActions)) {
    require_once __DIR__ . '/src/Views/header.php';
}

// Initialisation des contrôleurs
$recetteController = new RecetteController();
$contactController = new ContactController();
$userController = new UserController();
$favoriController = new FavoriController();

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
            case 'modifierProfil':
                $userController->modifierProfil();
                break;
            default:
                echo "Action non trouvée pour le contrôleur User";
        }
        break;
        
    case 'Favori':
    case 'favori':
        switch ($action) {
            case 'liste':
            case 'index':
                $favoriController->liste();
                break;
            case 'getFavoris':
                $favoriController->getFavoris();
                break;
            case 'ajouter':
                $favoriController->ajouter();
                break;
            case 'supprimer':
                $favoriController->supprimer();
                break;
            case 'toggle':
                $favoriController->toggle();
                break;
            default:
                echo "Action non trouvée pour le contrôleur Favori";
        }
        break;
        
    default:
        echo "Page non trouvée";
}

// Afficher le footer seulement si ce n'est pas une action JSON
if (!in_array($action, $jsonActions)) {
    require_once __DIR__ . '/src/Views/footer.php';
}
