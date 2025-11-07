<div class="row">
    <?php foreach ($recettes as $recipe) :
        // Vérifier si la recette est dans les favoris
        $estDansFavoris = isset($favorisIds) && in_array($recipe['id'], $favorisIds);
        $classeCoeur = $estDansFavoris ? 'bi-heart-fill text-danger' : 'bi-heart';
    ?>
        <div class="col-4 p-2">
            <div class="recipe card" data-id="<?php echo $recipe['id']; ?>">
                <?php 
                // Gestion de l'affichage de l'image
                $imageSrc = !empty($recipe['image']) ? htmlspecialchars($recipe['image']) : 'upload/no-image.png';
                ?>
                <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="Image de <?php echo htmlspecialchars($recipe['titre']); ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h2 class="card-title"><?php echo htmlspecialchars($recipe['titre']); ?></h2>
                    <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'], 0, 100)) . '...'; ?></p>
                    <p class="text-muted">Auteur : <a href="mailto:<?php echo htmlspecialchars($recipe['auteur']); ?>"><?php echo htmlspecialchars($recipe['auteur']); ?></a></p>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <a href="?c=Recette&a=detail&id=<?php echo $recipe['id']; ?>" class="btn btn-primary btn-sm">
                            Voir la recette
                        </a>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <div>
                                <span class="recipefav" data-id="<?php echo $recipe['id']; ?>" style="cursor: pointer; font-size: 1.5rem;" title="<?php echo $estDansFavoris ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                                    <i class="bi <?php echo $classeCoeur; ?>"></i>
                                </span>
                                <span class="recipeedit" data-id="<?php echo $recipe['id']; ?>" style="cursor: pointer; font-size: 1.5rem; margin-left: 10px;" title="Modifier la recette">
                                    <i class="bi bi-pencil-square text-primary"></i>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Gestion du clic sur les coeurs et les crayons
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des coeurs (favoris)
    let coeurs = document.querySelectorAll('.recipefav');
    
    coeurs.forEach(coeur => {
        coeur.addEventListener('click', function(e) {
            e.stopPropagation(); // Empêcher la propagation vers la carte
            let recetteId = this.dataset.id;
            
            // Lancer l'action toggle (ajouter ou retirer)
            fetch(`index.php?c=Favori&a=toggle&id=${recetteId}`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Recharger la page pour mettre à jour l'icône
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue.');
            });
        });
    });

    // Gestion des crayons (modification)
    let crayons = document.querySelectorAll('.recipeedit');
    
    crayons.forEach(crayon => {
        crayon.addEventListener('click', function(e) {
            e.stopPropagation(); // Empêcher la propagation vers la carte
            let recetteId = this.dataset.id;
            
            // Rediriger vers la page de modification
            window.location.href = `?c=Recette&a=modifier&id=${recetteId}`;
        });
    });
});
</script>