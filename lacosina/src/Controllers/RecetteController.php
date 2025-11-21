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
                // Affichage de la vue de confirmation de modification
                require_once(__DIR__ . '/../Views/Recette/modifie.php');
            } else {
                echo 'Erreur lors de la modification de la recette.';
            }
        } else {
            // Création d'une nouvelle recette
            $resultat = $this->recetteModel->add($titre, $description, $auteur, $imagePath, $type_plat);

            if ($resultat){
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
        
        // Utilisation du modèle pour récupérer les recettes
        if ($filtre && in_array($filtre, ['entree', 'plat', 'dessert'])) {
            $recettes = $this->recetteModel->findBy(['type_plat' => $filtre]);
        } else {
            $recettes = $this->recetteModel->findAll();
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
            header('Location: index.php?c=Recette&a=lister');
            exit;
        }
        
        // Vérifier que la recette existe
        $recette = $this->recetteModel->find($id);
        if (!$recette) {
            $_SESSION['message'] = 'Recette introuvable.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?c=Recette&a=lister');
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
            $_SESSION['message'] = 'Recette supprimée avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la suppression de la recette.';
            $_SESSION['message_type'] = 'danger';
        }
        
        header('Location: index.php?c=Recette&a=lister');
        exit;
    }
}


