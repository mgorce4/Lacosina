<?php

//import des classes contrôleurs
require_once(__DIR__ . '/src/Controllers/RecetteController.php');
require_once(__DIR__ . '/src/Controllers/ContactController.php');
// connexion à la base de données
require_once __DIR__ . '/src/Models/connectDb.php';

// header
require_once __DIR__ . '/src/Views/header.php';

// routage double: c=contrôleur & a=action
$controller = isset($_GET['c']) ? $_GET['c'] : 'home';
$action = isset($_GET['a']) ? $_GET['a'] : 'index';

// Initialisation des contrôleurs
$recetteController = new RecetteController($pdo);
$contactController = new ContactController($pdo);

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
        
    default:
        echo "Page non trouvée";
}

// footer
require_once __DIR__ . '/src/Views/footer.php';
