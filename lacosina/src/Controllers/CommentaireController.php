<?php

//connexion à la base de données
require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Commentaire.php');

class CommentaireController{
    private $commentaireModel;

    public function __construct(){
        $this->commentaireModel = new Commentaire();
    }

    //fonction permettant d'ajouter un commentaire
    function ajouter(){
        // Vérifier que la requête est en POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['message'] = 'Méthode non autorisée.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }

        // Récupérer les données du formulaire
        $recetteId = isset($_POST['recette_id']) ? intval($_POST['recette_id']) : 0;
        $commentaire = isset($_POST['commentaire']) ? trim($_POST['commentaire']) : '';

        // Déterminer le pseudo (utilisateur connecté ou anonyme)
        $pseudo = isset($_SESSION['identifiant']) ? $_SESSION['identifiant'] : 'Anonyme';

        // Validation
        if ($recetteId <= 0) {
            $_SESSION['message'] = 'Recette invalide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }

        if (empty($commentaire)) {
            $_SESSION['message'] = 'Le commentaire ne peut pas être vide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php?c=Recette&a=detail&id=' . $recetteId);
            exit;
        }

        // Ajouter le commentaire
        $result = $this->commentaireModel->add($recetteId, $pseudo, $commentaire);

        if ($result) {
            $_SESSION['message'] = 'Commentaire ajouté avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de l\'ajout du commentaire.';
            $_SESSION['message_type'] = 'danger';
        }

        // Rediriger vers le détail de la recette
        header('Location: index.php?c=Recette&a=detail&id=' . $recetteId);
        exit;
    }

    //fonction permettant de lister les commentaires d'une recette
    function lister($recetteId = null){
        // Si un recetteId est fourni, c'est pour une recette spécifique
        if ($recetteId !== null) {
            return $this->commentaireModel->findByRecetteId($recetteId);
        }
        
        // Sinon, c'est pour la vue admin : liste de tous les commentaires
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour accéder à cette page.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }
        
        require_once(__DIR__ . '/../Views/Commentaire/liste.php');
    }

    //fonction permettant de supprimer un commentaire
    function supprimer(){
        // Vérifier si l'utilisateur est admin
        if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1) {
            $_SESSION['message'] = 'Vous n\'avez pas les droits pour supprimer un commentaire.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }

        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id <= 0) {
            $_SESSION['message'] = 'ID de commentaire invalide.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }

        // Récupérer le commentaire pour avoir l'ID de la recette
        $commentaire = $this->commentaireModel->find($id);
        
        if (!$commentaire) {
            $_SESSION['message'] = 'Commentaire introuvable.';
            $_SESSION['message_type'] = 'danger';
            header('Location: index.php');
            exit;
        }

        $recetteId = $commentaire['recette_id'];

        // Supprimer le commentaire
        $result = $this->commentaireModel->delete($id);

        if ($result) {
            $_SESSION['message'] = 'Commentaire supprimé avec succès.';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'Erreur lors de la suppression du commentaire.';
            $_SESSION['message_type'] = 'danger';
        }

        // Rediriger vers le détail de la recette
        header('Location: index.php?c=Recette&a=detail&id=' . $recetteId);
        exit;
    }
}
