<?php if (isset($recette) && $recette): ?>
    <!-- DEBUG: Recette trouvée, ID = <?php echo $recette['id']; ?> -->
    <div class="row">
        <div class="col-12">
            <h1><?php echo htmlspecialchars($recette['titre']); ?></h1>
            
            <?php 
            // Gestion de l'affichage de l'image
            $imageSrc = !empty($recette['image']) ? htmlspecialchars($recette['image']) : 'upload/no-image.png';
            ?>
            <div class="text-center mb-4">
                <img src="<?php echo $imageSrc; ?>" class="img-fluid rounded" alt="Image de <?php echo htmlspecialchars($recette['titre']); ?>" style="max-height: 400px; object-fit: cover;">
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($recette['description'])); ?></p>
                    
                    <hr>
                    
                    <p class="text-muted">
                        <strong>Auteur :</strong> 
                        <a href="mailto:<?php echo htmlspecialchars($recette['auteur']); ?>" class="text-decoration-none">
                            <?php echo htmlspecialchars($recette['auteur']); ?>
                        </a>
                    </p>
                    
                    <?php if (isset($recette['date_creation'])): ?>
                        <p class="text-muted">
                            <strong>Date de création :</strong> 
                            <?php echo date('d/m/Y à H:i', strtotime($recette['date_creation'])); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Boutons d'action -->
            <div class="mt-4 mb-5">
                <div class="row">
                    <div class="col-12">
                        <a href="?c=Recette&a=lister" class="btn btn-primary me-3">
                            ← Retour à la liste des recettes
                        </a>
                        <?php
                        // Afficher le bouton de modification seulement si l'utilisateur est connecté
                        if (isset($_SESSION['user_id'])): ?>
                            <a href="?c=Recette&a=modifier&id=<?php echo $recette['id']; ?>" class="btn btn-primary">
                                Modifier cette recette
                            </a>
                            <?php if (isset($estDansFavoris) && $estDansFavoris): ?>
                                <button onclick="retirerDesFavoris(<?php echo $recette['id']; ?>)" class="btn btn-danger">
                                    <i class="bi bi-heart-fill"></i> Retirer des favoris
                                </button>
                            <?php else: ?>
                                <button onclick="ajouterAuxFavoris(<?php echo $recette['id']; ?>)" class="btn btn-warning">
                                    <i class="bi bi-heart"></i> Ajouter aux favoris
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section des commentaires -->
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="mb-4">Commentaires</h3>
            
            <!-- Bouton pour afficher le formulaire -->
            <button id="btnAjouterCommentaire" class="btn btn-success mb-4">
                <i class="bi bi-chat-left-text"></i> Ajouter un commentaire
            </button>

            <!-- Formulaire d'ajout de commentaire (caché par défaut) -->
            <div id="formCommentaire" style="display: none;" class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Nouveau commentaire</h5>
                    <form method="post" action="?c=Commentaire&a=ajouter">
                        <input type="hidden" name="recette_id" value="<?php echo $recette['id']; ?>">
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="4" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Valider le commentaire</button>
                        <button type="button" id="btnAnnuler" class="btn btn-secondary">Annuler</button>
                    </form>
                </div>
            </div>

            <!-- Liste des commentaires -->
            <div class="commentaires-list">
                <?php if (!empty($commentaires)): ?>
                    <?php foreach ($commentaires as $commentaire): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            <i class="bi bi-person-circle"></i> 
                                            <?php echo htmlspecialchars($commentaire['pseudo']); ?>
                                        </h6>
                                        <p class="card-text"><?php echo nl2br(htmlspecialchars($commentaire['commentaire'])); ?></p>
                                        <p class="text-muted small">
                                            <i class="bi bi-clock"></i> 
                                            <?php echo date('d/m/Y à H:i', strtotime($commentaire['create_time'])); ?>
                                        </p>
                                    </div>
                                    <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                        <a href="?c=Commentaire&a=supprimer&id=<?php echo $commentaire['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        Aucun commentaire sur cette recette
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Gestion de l'affichage du formulaire de commentaire
    document.getElementById('btnAjouterCommentaire').addEventListener('click', function() {
        document.getElementById('formCommentaire').style.display = 'block';
        this.style.display = 'none';
        document.getElementById('commentaire').focus();
    });

    document.getElementById('btnAnnuler').addEventListener('click', function() {
        document.getElementById('formCommentaire').style.display = 'none';
        document.getElementById('btnAjouterCommentaire').style.display = 'block';
        document.getElementById('commentaire').value = '';
    });

    function ajouterAuxFavoris(recetteId) {
        if (!recetteId || recetteId <= 0) {
            alert('ID de recette invalide.');
            return;
        }

        // Désactiver le bouton pendant la requête
        event.target.disabled = true;
        event.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Ajout en cours...';

        fetch(`index.php?c=Favori&a=ajouter&id=${recetteId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page pour mettre à jour le bouton
                location.reload();
            } else {
                alert(data.message);
                // Réactiver le bouton
                event.target.disabled = false;
                event.target.innerHTML = '<i class="bi bi-heart"></i> Ajouter aux favoris';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'ajout aux favoris.');
            // Réactiver le bouton
            event.target.disabled = false;
            event.target.innerHTML = '<i class="bi bi-heart"></i> Ajouter aux favoris';
        });
    }

    function retirerDesFavoris(recetteId) {
        if (!recetteId || recetteId <= 0) {
            alert('ID de recette invalide.');
            return;
        }

        if (!confirm('Voulez-vous vraiment retirer cette recette de vos favoris ?')) {
            return;
        }

        // Désactiver le bouton pendant la requête
        event.target.disabled = true;
        event.target.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Retrait en cours...';

        fetch(`index.php?c=Favori&a=supprimer&id=${recetteId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page pour mettre à jour le bouton
                location.reload();
            } else {
                alert(data.message);
                // Réactiver le bouton
                event.target.disabled = false;
                event.target.innerHTML = '<i class="bi bi-heart-fill"></i> Retirer des favoris';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors du retrait des favoris.');
            // Réactiver le bouton
            event.target.disabled = false;
            event.target.innerHTML = '<i class="bi bi-heart-fill"></i> Retirer des favoris';
        });
    }
    </script>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">Recette non trouvée</h4>
        <p>La recette demandée n'existe pas ou n'a pas pu être chargée.</p>
        <hr>
        <p class="mb-0">
            <a href="?c=Recette&a=lister" class="btn btn-primary">Retour à la liste des recettes</a>
        </p>
    </div>
<?php endif; ?>