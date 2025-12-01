<?php

// Autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/autoload.php';
}

use App\Controllers\{RecetteController, ContactController, UserController, FavoriController, CommentaireController, homeController};

// Configuration
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Session
session_status() === PHP_SESSION_NONE && session_start();

// Routage
$controller = $_GET['c'] ?? 'home';
$action = $_GET['a'] ?? 'index';

// Actions JSON (sans header/footer)
$jsonActions = ['getFavoris', 'modifierProfil', 'ajouter', 'supprimer', 'toggle'];
$isJson = in_array($action, $jsonActions);

// Header
!$isJson && require_once __DIR__ . '/src/Views/header.php';

// Dispatcher
try {
    $ctrl = strtolower($controller);
    
    // Mapping contrôleur => classe
    $controllers = [
        'home' => homeController::class,
        'recette' => RecetteController::class,
        'contact' => ContactController::class,
        'user' => UserController::class,
        'favori' => FavoriController::class,
        'commentaire' => CommentaireController::class,
    ];
    
    // Cas spéciaux rétrocompatibilité
    $legacyRoutes = [
        'enregistrer_contact' => [ContactController::class, 'enregistrer'],
        'ajout' => [RecetteController::class, 'ajouter'],
        'enregistrer' => [RecetteController::class, 'enregistrer'],
        'lister' => [RecetteController::class, 'index'],
        'detail' => [RecetteController::class, 'detail'],
    ];
    
    if (isset($legacyRoutes[$ctrl])) {
        [$class, $method] = $legacyRoutes[$ctrl];
        (new $class)->$method();
    } elseif (isset($controllers[$ctrl])) {
        (new $controllers[$ctrl])->$action();
    } else {
        throw new Exception('Contrôleur non trouvé');
    }
} catch (Exception $e) {
    if ($isJson) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
    echo '<div class="alert alert-danger mt-4" role="alert">';
    echo '<h4 class="alert-heading"><i class="bi bi-exclamation-octagon"></i> Erreur</h4>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<hr><a href="?c=home" class="btn btn-primary">Retour à l\'accueil</a>';
    echo '</div>';
}

// Footer
!$isJson && require_once __DIR__ . '/src/Views/footer.php';
