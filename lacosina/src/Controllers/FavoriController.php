<?php

//connexion à la base de données
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Favori.php');
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Recette.php');

class FavoriController{
    private $favoriModel;
    private $recetteModel;

    public function __construct(){
        $this->favoriModel = new Favori();
        $this->recetteModel = new Recette();
    }

    //fonction permettant d'ajouter une recette aux favoris
    function ajouter(){
        // Définir le header JSON
        header('Content-Type: application/json');
        
        try {
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour ajouter une recette aux favoris.']);
                exit;
            }

            // Récupérer les données
            $userId = $_SESSION['user_id'];
            $recetteId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['recette_id']) ? intval($_POST['recette_id']) : 0);

            // Vérifier que l'ID de la recette est valide
            if ($recetteId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de recette invalide.']);
                exit;
            }

            // Vérifier que la recette existe
            $recette = $this->recetteModel->find($recetteId);
            if (!$recette) {
                echo json_encode(['success' => false, 'message' => 'Cette recette n\'existe pas.']);
                exit;
            }

            // Vérifier si la recette n'est pas déjà dans les favoris (éviter les doublons)
            $dejaDansFavoris = $this->favoriModel->isFavorite($userId, $recetteId);
            
            if ($dejaDansFavoris) {
                echo json_encode(['success' => false, 'message' => 'Cette recette est déjà dans vos favoris.']);
                exit;
            }

            // Ajouter aux favoris
            $result = $this->favoriModel->add($recetteId, $userId);

            if ($result) {
                // Ajouter un message de succès dans la session
                $_SESSION['message'] = 'Recette ajoutée aux favoris avec succès.';
                $_SESSION['message_type'] = 'success';
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Recette ajoutée aux favoris avec succès.',
                    'favori_id' => $result
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout aux favoris.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    //fonction permettant de supprimer une recette des favoris
    function supprimer(){
        // Définir le header JSON
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer une recette des favoris.']);
            exit;
        }

        // Récupérer les données
        $userId = $_SESSION['user_id'];
        $recetteId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['recette_id']) ? intval($_POST['recette_id']) : 0);

        // Vérifier que l'ID de la recette est valide
        if ($recetteId <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID de recette invalide.']);
            exit;
        }

        // Supprimer des favoris
        $result = $this->favoriModel->deleteByRecetteAndUser($recetteId, $userId);

        if ($result) {
            // Ajouter un message de succès dans la session
            $_SESSION['message'] = 'Recette retirée des favoris';
            $_SESSION['message_type'] = 'info';
            
            echo json_encode([
                'success' => true, 
                'message' => 'Recette retirée des favoris'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cette recette n\'est pas dans vos favoris.']);
        }
        exit;
    }

    //fonction permettant d'afficher la liste des favoris d'un utilisateur
    function liste(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-warning">Vous devez être connecté pour voir vos favoris.</div>';
            echo '<a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>';
            return;
        }

        // Récupérer les favoris de l'utilisateur
        $userId = $_SESSION['user_id'];
        $favoris = $this->favoriModel->findByUserId($userId);

        // Charger la vue
        require_once(__DIR__ . '/../Views/User/favoris.php');
    }

    //fonction permettant de récupérer les favoris de l'utilisateur connecté au format JSON
    function getFavoris(){
        // Définir le header JSON
        header('Content-Type: application/json');
        
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour voir vos favoris.']);
            exit;
        }

        // Récupérer les favoris de l'utilisateur
        $userId = $_SESSION['user_id'];
        $favoris = $this->favoriModel->findByUserId($userId);

        // Retourner les favoris au format JSON
        echo json_encode([
            'success' => true,
            'count' => count($favoris),
            'favoris' => $favoris
        ]);
        exit;
    }

    //fonction permettant de basculer l'état favori (ajouter ou supprimer)
    function toggle(){
        // Définir le header JSON
        @header('Content-Type: application/json');
        
        try {
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour gérer vos favoris.']);
                exit;
            }

            // Récupérer les données
            $userId = $_SESSION['user_id'];
            $recetteId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_POST['recette_id']) ? intval($_POST['recette_id']) : 0);

            // Vérifier que l'ID de la recette est valide
            if ($recetteId <= 0) {
                echo json_encode(['success' => false, 'message' => 'ID de recette invalide.']);
                exit;
            }

            // Vérifier si la recette est déjà dans les favoris
            $dejaDansFavoris = $this->favoriModel->isFavorite($userId, $recetteId);
            
            if ($dejaDansFavoris) {
                // Supprimer des favoris
                $result = $this->favoriModel->deleteByRecetteAndUser($recetteId, $userId);
                if ($result) {
                    echo json_encode([
                        'success' => true, 
                        'action' => 'removed',
                        'message' => 'Recette retirée des favoris.',
                        'isFavorite' => false
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression.']);
                }
            } else {
                // Ajouter aux favoris
                $result = $this->favoriModel->add($recetteId, $userId);
                if ($result) {
                    echo json_encode([
                        'success' => true, 
                        'action' => 'added',
                        'message' => 'Recette ajoutée aux favoris.',
                        'isFavorite' => true,
                        'favori_id' => $result
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de l\'ajout.']);
                }
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
        }
        exit;
    }

    //fonction permettant de vérifier si une recette existe dans les favoris d'un utilisateur
    function existe($recetteId, $userId = null){
        // Si userId n'est pas fourni, utiliser l'utilisateur connecté
        if ($userId === null) {
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                return false;
            }
            $userId = $_SESSION['user_id'];
        }
        
        // Vérifier si la recette est dans les favoris
        return $this->favoriModel->isFavorite($userId, $recetteId);
    }
}
