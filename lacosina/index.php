<?php

// Activer l'affichage des erreurs temporairement pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 0); // 0 pour ne pas afficher dans le navigateur
ini_set('log_errors', 1);

// Démarrer la session AVANT tout affichage
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//import des classes contrôleurs
require_once(__DIR__ . '/src/Controllers/RecetteController.php');
require_once(__DIR__ . '/src/Controllers/ContactController.php');
require_once(__DIR__ . '/src/Controllers/UserController.php');
require_once(__DIR__ . '/src/Controllers/FavoriController.php');
require_once(__DIR__ . '/src/Controllers/CommentaireController.php');

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
$commentaireController = new CommentaireController();

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
            case 'supprimer':
                $recetteController->supprimer();
                break;
            default:
                if (!in_array($action, $jsonActions)) {
                    echo '<div class="alert alert-danger mt-4" role="alert">';
                    echo '<h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Action non trouvée</h4>';
                    echo '<p>L\'action demandée n\'existe pas pour le contrôleur Recette.</p>';
                    echo '<hr>';
                    echo '<a href="?c=Recette&a=lister" class="btn btn-primary">Retour aux recettes</a> ';
                    echo '<a href="?c=home" class="btn btn-secondary">Retour à l\'accueil</a>';
                    echo '</div>';
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
                    exit;
                }
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
                if (!in_array($action, $jsonActions)) {
                    echo '<div class="alert alert-danger mt-4" role="alert">';
                    echo '<h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Action non trouvée</h4>';
                    echo '<p>L\'action demandée n\'existe pas pour le contrôleur User.</p>';
                    echo '<hr>';
                    echo '<a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a> ';
                    echo '<a href="?c=home" class="btn btn-secondary">Retour à l\'accueil</a>';
                    echo '</div>';
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
                    exit;
                }
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
                if (!in_array($action, $jsonActions)) {
                    echo '<div class="alert alert-danger mt-4" role="alert">';
                    echo '<h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Action non trouvée</h4>';
                    echo '<p>L\'action demandée n\'existe pas pour le contrôleur Favori.</p>';
                    echo '<hr>';
                    echo '<a href="?c=Favori&a=liste" class="btn btn-primary">Mes favoris</a> ';
                    echo '<a href="?c=home" class="btn btn-secondary">Retour à l\'accueil</a>';
                    echo '</div>';
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
                    exit;
                }
        }
        break;
        
    case 'Commentaire':
    case 'commentaire':
        switch ($action) {
            case 'liste':
            case 'index':
                $commentaireController->lister();
                break;
            case 'ajouter':
                $commentaireController->ajouter();
                break;
            case 'supprimer':
                $commentaireController->supprimer();
                break;
            default:
                if (!in_array($action, $jsonActions)) {
                    echo '<div class="alert alert-danger mt-4" role="alert">';
                    echo '<h4 class="alert-heading"><i class="bi bi-exclamation-triangle"></i> Action non trouvée</h4>';
                    echo '<p>L\'action demandée n\'existe pas pour le contrôleur Commentaire.</p>';
                    echo '<hr>';
                    echo '<a href="?c=Commentaire&a=liste" class="btn btn-primary">Liste des commentaires</a> ';
                    echo '<a href="?c=home" class="btn btn-secondary">Retour à l\'accueil</a>';
                    echo '</div>';
                } else {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Action non trouvée']);
                    exit;
                }
        }
        break;
        
    default:
        echo '<div class="alert alert-danger mt-4" role="alert">';
        echo '<h4 class="alert-heading"><i class="bi bi-exclamation-octagon"></i> Page non trouvée</h4>';
        echo '<p>La page ou le contrôleur demandé n\'existe pas.</p>';
        echo '<hr>';
        echo '<p class="mb-0"><a href="?c=home" class="btn btn-primary">Retour à l\'accueil</a> ';
        echo '<a href="?c=Recette&a=lister" class="btn btn-secondary">Voir les recettes</a></p>';
        echo '</div>';
}

// Afficher le footer seulement si ce n'est pas une action JSON
if (!in_array($action, $jsonActions)) {
    require_once __DIR__ . '/src/Views/footer.php';
}
