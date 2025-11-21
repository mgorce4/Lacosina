<!-- Filtres de type de plat -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex gap-3 justify-content-center">
            <div class="card filter-card bg-primary-subtle" data-filter="all" style="cursor: pointer; min-width: 150px;">
                <div class="card-body text-center py-2">
                    <i class="bi bi-grid-3x3-gap-fill"></i> Toutes les recettes
                </div>
            </div>
            <div class="card filter-card" data-filter="entree" style="cursor: pointer; min-width: 150px;">
                <div class="card-body text-center py-2">
                    <i class="bi bi-egg-fried"></i> Entrées
                </div>
            </div>
            <div class="card filter-card" data-filter="plat" style="cursor: pointer; min-width: 150px;">
                <div class="card-body text-center py-2">
                    <i class="bi bi-dish-fill"></i> Plats
                </div>
            </div>
            <div class="card filter-card" data-filter="dessert" style="cursor: pointer; min-width: 150px;">
                <div class="card-body text-center py-2">
                    <i class="bi bi-cake2-fill"></i> Desserts
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des recettes -->
<div class="row" id="listeRecettes">
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
                                <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                    <span class="recipedelete" data-id="<?php echo $recipe['id']; ?>" style="cursor: pointer; font-size: 1.5rem; margin-left: 10px;" title="Supprimer la recette">
                                        <i class="bi bi-trash text-danger"></i>
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Fonction pour initialiser les événements sur les recettes
function initRecetteEvents() {
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

    // Gestion des poubelles (suppression - admin uniquement)
    let poubelles = document.querySelectorAll('.recipedelete');
    
    poubelles.forEach(poubelle => {
        poubelle.addEventListener('click', function(e) {
            e.stopPropagation(); // Empêcher la propagation vers la carte
            let recetteId = this.dataset.id;
            
            // Confirmation avant suppression
            if (confirm('Êtes-vous sûr de vouloir supprimer cette recette ? Cette action est irréversible et supprimera également tous les favoris et commentaires associés.')) {
                // Rediriger vers l'action de suppression
                window.location.href = `?c=Recette&a=supprimer&id=${recetteId}`;
            }
        });
    });

    // Gestion du clic sur les cartes de recettes
    let cards = document.querySelectorAll('.recipe');
    cards.forEach(card => {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function() {
            let recetteId = this.dataset.id;
            window.location.href = `?c=Recette&a=detail&id=${recetteId}`;
        });
    });
}

// Gestion des filtres
function initFilters() {
    let filterCards = document.querySelectorAll('.filter-card');
    
    filterCards.forEach(card => {
        card.addEventListener('mouseover', function() {
            if (!this.classList.contains('bg-primary-subtle')) {
                this.classList.add('bg-light');
            }
        });
        
        card.addEventListener('mouseout', function() {
            if (!this.classList.contains('bg-primary-subtle')) {
                this.classList.remove('bg-light');
            }
        });
        
        card.addEventListener('click', function() {
            let filter = this.dataset.filter;
            
            // Retirer la classe active de tous les filtres
            filterCards.forEach(f => f.classList.remove('bg-primary-subtle'));
            
            // Ajouter la classe active au filtre cliqué
            this.classList.add('bg-primary-subtle');
            
            // Charger les recettes filtrées
            let url = filter === 'all' ? 'index.php?c=Recette&a=index' : `index.php?c=Recette&a=index&filtre=${filter}`;
            
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    // Parser le HTML
                    let parser = new DOMParser();
                    let doc = parser.parseFromString(html, 'text/html');
                    
                    // Récupérer la div listeRecettes
                    let nouvelleListe = doc.querySelector('#listeRecettes');
                    
                    if (nouvelleListe) {
                        // Remplacer le contenu
                        document.getElementById('listeRecettes').innerHTML = nouvelleListe.innerHTML;
                        
                        // Réinitialiser les événements
                        initRecetteEvents();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du filtrage:', error);
                    alert('Une erreur est survenue lors du filtrage des recettes.');
                });
        });
    });
}

// Initialisation au chargement du DOM
document.addEventListener('DOMContentLoaded', function() {
    initRecetteEvents();
    initFilters();
});
</script>