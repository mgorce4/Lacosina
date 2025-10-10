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
        require_once(__DIR__ . '/../Views/Recette/ajout.php');
    }

    //fonction permettant d'enregistrer une nouvelle recette
    function enregistrer(){
        //récupération des données de formulaire
        $titre = $_POST['titre'];
        $description = $_POST['description'];
        $auteur = $_POST['auteur'];
        $image = isset($_FILES['image']) ? $_FILES['image'] : null;
        
        //Gestion de l'upload de l'image
        $imagePath = null;
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
        
        // Si aucune image n'est uploadée, utiliser l'image par défaut
        if ($imagePath === null) {
            $imagePath = 'upload/no-image.png';
        }

        //utilisation du modèle pour enregistrer
        $resultat = $this->recetteModel->add($titre, $description, $auteur, $imagePath);

        if ($resultat){
            require_once(__DIR__ . '/../Views/Recette/enregistrer.php');
        } else {
            echo 'Erreur lors de l\'enregistrement de la recette.';
        }
    }

    //fonction permettant de lister toutes les recettes
    function index(){
        //utilisation du modèle pour récupérer les recettes
        $recettes = $this->recetteModel->findAll();

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

        require_once(__DIR__ . '/../Views/Recette/detail.php');
    }
}

