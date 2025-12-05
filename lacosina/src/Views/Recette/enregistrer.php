<?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading"><i class="bi bi-check-circle"></i> Recette ajoutée avec succès !</h4>
        <p>La recette a été ajoutée et est immédiatement visible par tous les utilisateurs.</p>
        <hr>
        <div class="d-flex gap-2">
            <a href="?c=Recette&a=index" class="btn btn-primary">Voir toutes les recettes</a>
            <a href="?c=ajout" class="btn btn-secondary">Ajouter une autre recette</a>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info" role="alert">
        <h4 class="alert-heading"><i class="bi bi-clock-history"></i> Recette proposée avec succès !</h4>
        <p>Votre recette a été soumise et est en attente d'approbation par un administrateur.</p>
        <p class="mb-0">Vous serez notifié une fois qu'elle sera approuvée et visible par tous.</p>
        <hr>
        <div class="d-flex gap-2">
            <a href="?c=home" class="btn btn-primary">Retour à l'accueil</a>
            <a href="?c=ajout" class="btn btn-secondary">Proposer une autre recette</a>
        </div>
    </div>
<?php endif; ?>