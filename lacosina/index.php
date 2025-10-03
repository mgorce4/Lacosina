<?php
// connexion à la base de données
require_once __DIR__ . '/src/Models/connectDb.php';

// Chargement des contrôleurs
require_once __DIR__ . '/src/Controllers/RecetteController.php';

// header
require_once __DIR__ . '/src/Views/header.php';

// routage simple
$route = isset($_GET['c']) ? $_GET['c'] : 'home';

// Initialisation du contrôleur de recettes
$recetteController = new RecetteController($pdo);

switch ($route) {
    case 'home':
        require_once __DIR__ . '/src/Controllers/homeController.php';
        break;
    case 'contact':
        require_once __DIR__ . '/src/Controllers/contactController.php';
        break;
    case 'ajout':
        $recetteController->ajouter();
        break;
    case 'enregistrer':
        $recetteController->enregistrer();
        break;
    case 'lister':
        $recetteController->lister();
        break;
    case 'enregistrer_contact':
        require_once __DIR__ . '/src/Controllers/enregistrer_contactController.php';
        break;
    default:
        echo "Page non trouvée";
}

// footer
require_once __DIR__ . '/src/Views/footer.php';
