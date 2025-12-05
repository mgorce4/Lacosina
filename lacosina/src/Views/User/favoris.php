<?php if (isset($_SESSION['user_id'])): ?>
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-heart-fill text-danger"></i> Mes recettes favorites
            </h1>
            
            <!-- Div pour lister les recettes favorites avec data-id -->
            <div id="liste-favoris" data-id="<?php echo $_SESSION['user_id']; ?>">
                <?php if (empty($favoris)): ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Vous n'avez pas encore de recettes favorites.
                    <br>
                    <a href="?c=Recette&a=index" class="alert-link">Découvrir les recettes</a>
                </div>
            <?php else: ?>
                <p class="text-muted mb-4">
                    Vous avez <?php echo count($favoris); ?> recette<?php echo count($favoris) > 1 ? 's' : ''; ?> favorite<?php echo count($favoris) > 1 ? 's' : ''; ?>
                </p>
                
                <div class="row">
                    <?php foreach ($favoris as $favori): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100">
                                <?php 
                                // Gestion de l'affichage de l'image
                                $imageSrc = !empty($favori['image']) ? htmlspecialchars($favori['image']) : 'upload/no-image.png';
                                ?>
                                <img src="<?php echo $imageSrc; ?>" 
                                     class="card-img-top" 
                                     alt="Image de <?php echo htmlspecialchars($favori['titre']); ?>" 
                                     style="height: 200px; object-fit: cover;"
                                     onerror="this.src='upload/no-image.png'">
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">
                                        <?php echo htmlspecialchars($favori['titre']); ?>
                                    </h5>
                                    <p class="card-text flex-grow-1">
                                        <?php echo htmlspecialchars(substr($favori['description'], 0, 100)) . '...'; ?>
                                    </p>
                                    <p class="text-muted small">
                                        <i class="bi bi-person"></i> Auteur : <?php echo htmlspecialchars($favori['auteur']); ?>
                                    </p>
                                    <p class="text-muted small">
                                        <i class="bi bi-calendar"></i> Ajouté le : <?php echo date('d/m/Y', strtotime($favori['create_time'])); ?>
                                    </p>
                                    
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="?c=Recette&a=detail&id=<?php echo $favori['recette_id']; ?>" 
                                           class="btn btn-primary btn-sm flex-grow-1">
                                            <i class="bi bi-eye"></i> Voir la recette
                                        </a>
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="retirerDesFavoris(<?php echo $favori['recette_id']; ?>)"
                                                title="Retirer des favoris">
                                            <i class="bi bi-heart-fill"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            </div>
            <!-- Fin de la div liste-favoris -->
            
            <hr>
            <div class="mt-4 mb-4">
                <a href="?c=Recette&a=index" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Retour aux recettes
                </a>
                <a href="?c=home" class="btn btn-outline-secondary">
                    <i class="bi bi-house"></i> Accueil
                </a>
            </div>
        </div>
    </div>

    <script>
    function retirerDesFavoris(recetteId) {
        if (!confirm('Voulez-vous vraiment retirer cette recette de vos favoris ?')) {
            return;
        }

        fetch(`index.php?c=Favori&a=supprimer&id=${recetteId}`, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Recharger la page pour mettre à jour la liste
                location.reload();
            } else {
                alert(data.message || 'Erreur lors de la suppression du favori.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue.');
        });
    }
    </script>

<?php else: ?>
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> Vous devez être connecté pour accéder à vos favoris.
    </div>
    <a href="?c=User&a=connexion" class="btn btn-primary">
        <i class="bi bi-box-arrow-in-right"></i> Se connecter
    </a>
<?php endif; ?>
