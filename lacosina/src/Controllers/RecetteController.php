<?php

//connexion à la base de données
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Recette.php');

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
            $resultat = $this->recetteModel->update($id, $titre, $description, $auteur, $imagePath);
            
            if ($resultat) {
                // Affichage de la vue de confirmation de modification
                require_once(__DIR__ . '/../Views/Recette/modifie.php');
            } else {
                echo 'Erreur lors de la modification de la recette.';
            }
        } else {
            // Création d'une nouvelle recette
            $resultat = $this->recetteModel->add($titre, $description, $auteur, $imagePath);

            if ($resultat){
                require_once(__DIR__ . '/../Views/Recette/enregistrer.php');
            } else {
                echo 'Erreur lors de l\'enregistrement de la recette.';
            }
        }
    }

    //fonction permettant de lister toutes les recettes
    function index(){
        //utilisation du modèle pour récupérer les recettes
        $recettes = $this->recetteModel->findAll();

        // Récupérer les IDs des favoris de l'utilisateur connecté
        $favorisIds = [];
        if (isset($_SESSION['user_id'])) {
            require_once(__DIR__ . '/FavoriController.php');
            $favoriController = new FavoriController();
            // On va créer un tableau avec les IDs des recettes en favoris
            require_once(__DIR__ . '/../Models/Favori.php');
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
            require_once(__DIR__ . '/FavoriController.php');
            $favoriController = new FavoriController();
            $estDansFavoris = $favoriController->existe($id, $_SESSION['user_id']);
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
}

