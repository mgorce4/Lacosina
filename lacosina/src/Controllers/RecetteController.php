<?php

namespace App\Controllers;

use App\Models\Recette;
use App\Models\Favori;
use App\Models\Commentaire;

class RecetteController{
    private $recetteModel;

    public function __construct(){
        $this->recetteModel = new Recette();
    }

    //fonction permettant d'ajouter une nouvelle recette
    function ajouter(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-warning">Vous devez être connecté pour ajouter une recette.</div>';
            echo '<a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>';
            return;
        }
        
        require_once(__DIR__ . '/../Views/Recette/ajout.php');
    }

    //fonction permettant d'enregistrer une nouvelle recette ou modifier une existante
    function enregistrer(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-warning">Vous devez être connecté pour effectuer cette action.</div>';
            echo '<a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>';
            return;
        }
        
        //récupération des données de formulaire
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $auteur = $_POST['auteur'];
        $type_plat = isset($_POST['type_plat']) ? $_POST['type_plat'] : 'entree';
        $image = isset($_FILES['image']) ? $_FILES['image'] : null;
        
        // Vérifier s'il s'agit d'une modification (présence d'un ID)
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $isModification = $id > 0;
        
        // Gestion de l'image selon création ou modification
        $imagePath = null;
        
        if ($isModification) {
            // Mode modification : récupérer l'ancienne recette
            $ancienneRecette = $this->recetteModel->find($id);
            $imagePath = $ancienneRecette['image']; // Conserver l'ancienne image par défaut
        }
        
        //Gestion de l'upload de l'image
        if ($image && $image['error'] === UPLOAD_ERR_OK && $image['size'] > 0){
            // Dossier upload à la racine du site
            $uploadDir = __DIR__ . '/../../upload/';
            if (!is_dir($uploadDir)){
                mkdir($uploadDir, 0755, true);
            }
            
            // Générer un nom unique pour éviter les conflits
            $extension = pathinfo($image['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '.' . $extension;
            $fullPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($image['tmp_name'], $fullPath)) {
                $imagePath = 'upload/' . $fileName; // Chemin relatif pour la base de données
            }
        }
        
        // Si aucune image et mode création, utiliser l'image par défaut
        if ($imagePath === null && !$isModification) {
            $imagePath = 'upload/no_image.png';
        }

        // Choisir entre création ou modification
        if ($isModification) {
            // Modification de la recette existante
            $resultat = $this->recetteModel->update($id, $titre, $description, $auteur, $imagePath, $type_plat);
            
            if ($resultat) {
                // Journalisation de la modification
                if (isset($GLOBALS['logger'])) {
                    $GLOBALS['logger']->info('Modification de recette', [
                        'recette_id' => $id,
                        'titre' => $titre,
                        'auteur' => $auteur,
                        'type_plat' => $type_plat,
                        'user_id' => $_SESSION['user_id'] ?? 'unknown'
                    ]);
                }
                
                // Affichage de la vue de confirmation de modification
                require_once(__DIR__ . '/../Views/Recette/modifie.php');
            } else {
                echo 'Erreur lors de la modification de la recette.';
            }
        } else {
            // Création d'une nouvelle recette
            // Si l'utilisateur est admin, la recette est approuvée directement
            $isApproved = (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1) ? 1 : 0;
            $resultat = $this->recetteModel->add($titre, $description, $auteur, $imagePath, $type_plat, $isApproved);

            if ($resultat){
                // Journalisation de la création
                if (isset($GLOBALS['logger'])) {
                    $GLOBALS['logger']->info('Création de recette', [
                        'titre' => $titre,
                        'auteur' => $auteur,
                        'type_plat' => $type_plat,
                        'isApproved' => $isApproved,
                        'user_id' => $_SESSION['user_id'] ?? 'unknown'
                    ]);
                }
                
                require_once(__DIR__ . '/../Views/Recette/enregistrer.php');
            } else {
                echo 'Erreur lors de l\'enregistrement de la recette.';
            }
        }
    }

    //fonction permettant de lister toutes les recettes
    function index(){
        // Vérifier s'il y a un filtre
        $filtre = isset($_GET['filtre']) ? $_GET['filtre'] : null;
        
        // Les admins voient toutes les recettes, les autres uniquement les approuvées
        $isAdmin = (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1);
        
        // Utilisation du modèle pour récupérer les recettes
        if ($filtre && in_array($filtre, ['entree', 'plat', 'dessert'])) {
            $recettes = $this->recetteModel->findBy(['type_plat' => $filtre]);
            // Filtrer pour les non-admins
            if (!$isAdmin) {
                $recettes = array_filter($recettes, function($r) { return $r['isApproved'] == 1; });
            }
        } else {
            $recettes = $isAdmin ? $this->recetteModel->findAll() : $this->recetteModel->findAllApproved();
        }

        // Récupérer les IDs des favoris de l'utilisateur connecté
        $favorisIds = [];
        if (isset($_SESSION['user_id'])) {
            $favoriModel = new Favori();
            $favoris = $favoriModel->findByUserId($_SESSION['user_id']);
            foreach ($favoris as $favori) {
                $favorisIds[] = $favori['recette_id'];
            }
        }

        require_once(__DIR__ . '/../Views/Recette/liste.php');
    }

    //fonction permettant d'afficher le détail d'une recette
    function detail(){
        // Récupération et validation de l'ID depuis $_GET['id']
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id > 0) {
            //utilisation du modèle pour récupérer une recette spécifique
            $recette = $this->recetteModel->find($id);
            
            // Vérification que la recette existe
            if (!$recette) {
                $recette = null; // Recette non trouvée
            }
        } else {
            $recette = null; // ID invalide
        }

        // Vérifier si la recette est dans les favoris de l'utilisateur connecté
        $estDansFavoris = false;
        if (isset($_SESSION['user_id']) && $recette) {
            $favoriModel = new Favori();
            $estDansFavoris = $favoriModel->isFavorite($_SESSION['user_id'], $id);
        }

        // Charger les commentaires de la recette
        $commentaires = [];
        if ($recette) {
            $commentaireModel = new Commentaire();
            $commentaires = $commentaireModel->findByRecetteId($id);
        }

        require_once(__DIR__ . '/../Views/Recette/detail.php');
    }

    //fonction permettant d'afficher le formulaire de modification d'une recette
    function modifier(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            echo '<div class="alert alert-warning">Vous devez être connecté pour modifier une recette.</div>';
            echo '<a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>';
            return;
        }
        
        // Récupération et validation de l'ID depuis $_GET['id']
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id > 0) {
            //utilisation du modèle pour récupérer la recette à modifier
            $recette = $this->recetteModel->find($id);
            
            // Vérification que la recette existe
            if (!$recette) {
                $recette = null; // Recette non trouvée
            }
        } else {
            $recette = null; // ID invalide
        }

        require_once(__DIR__ . '/../Views/Recette/modif.php');
    }

    //fonction permettant de supprimer une recette (admin uniquement)
    function supprimer(){
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour supprimer une recette.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }
        
        // Récupération et validation de l'ID depuis $_GET['id']
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $_SESSION['message'] = 'ID de recette invalide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?c=Recette&a=index');
            exit;
        }
        
        // Vérifier que la recette existe
        $recette = $this->recetteModel->find($id);
        if (!$recette) {
            $_SESSION['message'] = 'Recette introuvable.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?c=Recette&a=index');
            exit;
        }
        
        // Supprimer d'abord les favoris associés
        $favoriModel = new Favori();
        $favoriModel->deleteByRecetteAndUser($id, null); // Supprimer tous les favoris de cette recette
        
        // Supprimer les commentaires associés
        $commentaireModel = new Commentaire();
        // On utilise findByRecetteId pour récupérer tous les commentaires puis les supprimer un par un
        $commentaires = $commentaireModel->findByRecetteId($id);
        foreach ($commentaires as $commentaire) {
            $commentaireModel->delete($commentaire['id']);
        }
        
        // Supprimer la recette
        $resultat = $this->recetteModel->delete($id);
        
        if ($resultat) {
            // Journalisation de la suppression
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->info('Suppression de recette', [
                    'recette_id' => $id,
                    'titre' => $recette['titre'],
                    'user_id' => $_SESSION['user_id'] ?? 'unknown',
                    'admin' => $_SESSION['identifiant'] ?? 'unknown'
                ]);
            }
            
            $_SESSION['message'] = 'Recette supprimée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la suppression de la recette.';
            $_SESSION['message_type'] = 'danger';
        }
        
        header('Location: index.php?c=Recette&a=index');
        exit;
    }

    //fonction permettant de retourner toutes les recettes au format JSON
    function indexJSON(){
        // Définir le header pour indiquer du JSON
        header('Content-Type: application/json');
        
        // Les admins voient toutes les recettes, les autres uniquement les approuvées
        $isAdmin = (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1);
        $recettes = $isAdmin ? $this->recetteModel->findAll() : $this->recetteModel->findAllApproved();
        
        // Retourner les recettes en JSON
        echo json_encode($recettes);
        exit;
    }

    //fonction permettant d'afficher les recettes en attente d'approbation (admin uniquement)
    function attente(){
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour accéder à cette page.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }
        
        // Récupérer les recettes en attente
        $recettesEnAttente = $this->recetteModel->findAllPending();
        
        require_once(__DIR__ . '/../Views/Recette/attente.php');
    }

    //fonction permettant d'approuver une recette (admin uniquement)
    function approuver(){
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour effectuer cette action.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }
        
        // Récupérer l'ID de la recette
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $_SESSION['message'] = 'ID de recette invalide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?c=Recette&a=attente');
            exit;
        }
        
        // Approuver la recette
        $resultat = $this->recetteModel->approve($id);
        
        if ($resultat) {
            // Journalisation
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->info('Approbation de recette', [
                    'recette_id' => $id,
                    'admin' => $_SESSION['identifiant'] ?? 'unknown'
                ]);
            }
            
            $_SESSION['message'] = 'Recette approuvée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de l\'approbation de la recette.';
            $_SESSION['message_type'] = 'danger';
        }
        
        header('Location: index.php?c=Recette&a=attente');
        exit;
    }

    //fonction permettant d'afficher les recettes à approuver (admin uniquement) - alias de attente
    function aApprouver(){
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour accéder à cette page.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }
        
        // Récupérer les recettes en attente
        $recettesEnAttente = $this->recetteModel->findAllPending();
        
        require_once(__DIR__ . '/../Views/Recette/aApprouver.php');
    }

    //fonction permettant de valider une recette (admin uniquement) - alias de approuver
    function valider(){
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour effectuer cette action.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }
        
        // Récupérer l'ID de la recette
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        
        if ($id <= 0) {
            $_SESSION['message'] = 'ID de recette invalide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?c=Recette&a=aApprouver');
            exit;
        }
        
        // Valider la recette
        $resultat = $this->recetteModel->approve($id);
        
        if ($resultat) {
            // Journalisation
            if (isset($GLOBALS['logger'])) {
                $GLOBALS['logger']->info('Validation de recette', [
                    'recette_id' => $id,
                    'admin' => $_SESSION['identifiant'] ?? 'unknown'
                ]);
            }
            
            $_SESSION['message'] = 'Recette validée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la validation de la recette.';
            $_SESSION['message_type'] = 'danger';
        }
        
        header('Location: index.php?c=Recette&a=aApprouver');
        exit;
    }

    //fonction permettant d'afficher les recettes non validées pour l'utilisateur connecté
    function nonValidesPourUtilisateur(){
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['message'] = 'Vous devez être connecté pour accéder à cette page.';
            $_SESSION['message_type'] = 'warning';
            header('Location: index.php?c=User&a=connexion');
            exit;
        }
        
        // Récupérer l'email de l'utilisateur connecté
        $auteur = $_SESSION['mail'];
        
        // Récupérer les recettes non validées de l'utilisateur
        $recettesEnCours = $this->recetteModel->findPendingByAuthor($auteur);
        
        require_once(__DIR__ . '/../Views/Recette/enCoursValidation.php');
    }

    //fonction permettant de compter le nombre de recettes non validées (pour les notifications)
    function compterNonValidees(){
        // Définir le header pour indiquer du JSON
        header('Content-Type: application/json');
        
        // Compter les recettes en attente
        $count = $this->recetteModel->countPending();
        
        // Retourner le comptage en JSON
        echo json_encode(['count' => $count]);
        exit;
    }
}








