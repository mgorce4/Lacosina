<?php
// connexion à la base de données
require_once __DIR__ . '/src/Models/connectDb.php';

// header
require_once __DIR__ . '/src/Views/header.php';

// routage simple
$route = isset($_GET['c']) ? $_GET['c'] : 'home';

switch ($route) {
    case 'home':
        require_once __DIR__ . '/src/Controllers/homeController.php';
        break;
    case 'contact':
        require_once __DIR__ . '/src/Controllers/contactController.php';
        break;
    case 'ajout':
        require_once __DIR__ . '/src/Controllers/ajoutController.php';
        break;
    case 'enregistrer':
        require_once __DIR__ . '/src/Controllers/enregistrerController.php';
        break;
    default:
        echo "Page non trouvée";
}

// footer
require_once __DIR__ . '/src/Views/footer.php';
