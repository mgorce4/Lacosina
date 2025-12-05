<?php if (isset($recette) && $recette): ?>
<h1>Modifier la recette : <?php echo htmlspecialchars($recette['titre']); ?></h1>
<form action="?c=Recette&a=enregistrer&id=<?php echo $recette['id']; ?>" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre de la recette</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($recette['titre']); ?>" name="titre" id="titre" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description de la recette</label>
        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($recette['description']); ?></textarea>
    </div>
    <div class="mb-3">
        <label for="auteur" class="form-label">Mail de l'auteur</label>
        <input type="email" class="form-control" value="<?php echo htmlspecialchars($recette['auteur']); ?>" name="auteur" id="auteur" required>
    </div>

    <div class="mb-3">
        <label for="type_plat" class="form-label">Type de plat</label>
        <select class="form-select" name="type_plat" id="type_plat" required>
            <option value="entree" <?php echo (isset($recette['type_plat']) && $recette['type_plat'] == 'entree') ? 'selected' : ''; ?>>Entrée</option>
            <option value="plat" <?php echo (isset($recette['type_plat']) && $recette['type_plat'] == 'plat') ? 'selected' : ''; ?>>Plat</option>
            <option value="dessert" <?php echo (isset($recette['type_plat']) && $recette['type_plat'] == 'dessert') ? 'selected' : ''; ?>>Dessert</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="image" class="form-label">Image de la recette <br>(pour la modifier merci de choisir la nouvelle image)</label>
        
        <div class="mb-2">
            <p><small class="text-muted">Image actuelle :</small></p>
            <?php 
            $imageSrc = !empty($recette['image']) ? htmlspecialchars($recette['image']) : 'upload/no-image.png';
            ?>
            <img class="rounded w-25 mx-auto img-fluid" src="<?php echo $imageSrc; ?>" alt="<?php echo htmlspecialchars($recette['titre']); ?>" class="card-img-top">
        </div>
        
        <input type="file" class="form-control" name="image" id="image" accept="image/*">
        <div class="form-text">Laissez vide pour conserver l'image actuelle.</div>
    </div>
    
    <div class="mb-3">
        <button type="submit" class="btn btn-primary me-2" id="enregistrer">
            <i class="bi bi-check-circle"></i> Modifier
        </button>
        <a href="?c=Recette&a=detail&id=<?php echo $recette['id']; ?>" class="btn btn-secondary">
            <i class="bi bi-x-circle"></i> Annuler
        </a>
    </div>
</form>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">Recette non trouvée</h4>
        <p>La recette demandée n'existe pas ou n'a pas pu être chargée.</p>
        <hr>
        <p class="mb-0">
            <a href="?c=Recette&a=index" class="btn btn-primary">Retour à la liste des recettes</a>
        </p>
    </div>
<?php endif; ?>