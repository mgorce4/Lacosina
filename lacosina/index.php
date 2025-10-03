<?php

//import de la classe RecetteController
require_once(__DIR__ . '/src/Controllers/RecetteController.php');
// connexion à la base de données
require_once __DIR__ . '/src/Models/connectDb.php';

// header
require_once __DIR__ . '/src/Views/header.php';

// routage double: c=contrôleur & a=action
$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';

// Initialisation du contrôleur de recettes
$recetteController = new RecetteController($pdo);

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
            default:
                echo "Action non trouvée pour le contrôleur Recette";
        }   
        break;
        
    case 'home':
        require_once __DIR__ . '/src/Controllers/homeController.php';
        break;
        
    case 'contact':
        require_once __DIR__ . '/src/Controllers/contactController.php';
        break;
        
    case 'enregistrer_contact':
        require_once __DIR__ . '/src/Controllers/enregistrer_contactController.php';
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
        
    default:
        echo "Page non trouvée";
}

// footer
require_once __DIR__ . '/src/Views/footer.php';
