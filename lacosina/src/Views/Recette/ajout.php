<h1>Ajouter une recette</h1>
<form action="?c=Recette&a=enregistrer" method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label for= "image" class="form-label">Image de la recette (optionnel)</label>
        <input type="file" class="form-control"  name="image" id="image" accept="image/*">
        <div class="form-text">Si aucune image n'est sélectionnée, une image par défaut sera utilisée.</div>
    </div>
    <div class="mb-3">
        <label for= "titre" class="form-label">Titre de la recette</label>
        <input type="text" class="form-control"  name="titre" id="titre" required>
    </div>
    <div class="mb-3">
        <label for="descrption" class="form-label">Description de la recette</label>
        <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label for="type_plat" class="form-label">Type de plat</label>
        <select class="form-select" name="type_plat" id="type_plat" required>
            <option value="entree">Entrée</option>
            <option value="plat">Plat</option>
            <option value="dessert">Dessert</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="ingredients" class="form-label">Mail de l'auteur</label>
        <textarea class="form-control" name="auteur" id="auteur"  required></textarea>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-primary" id="enregistrer" >Enregistrer</button>
    </div>
</form>