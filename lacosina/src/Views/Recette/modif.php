<h1>Modifier la recette : <?php echo $recipe['titre']; ?></h1>
<form action="?c=Recette&a=enregistrer&id=<?php echo $recipe['id']; ?>" method="post" enctype = "multipart/form-data">
    <div class="mb-3">
        <label for="titre" class="form-label">Titre de la recette</label>
        <input type="text" class="form-control" value="<?php echo $recipe['titre']; ?>" name="titre" id="titre" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description de la recette</label>
        <textarea class="form-control" id="description" name="description" rows="3" required><?php echo $recipe['description']; ?></textarea>
    </div>
    <div class="mb-3">
        <label for="auteur" class="form-label">Mail de l'auteur</label>
        <input type="email" class="form-control" value="<?php echo $recipe['auteur']; ?>" name="auteur" id="auteur" required>
    </div>

    <div class="mb-3">
        
    </div>
</form>