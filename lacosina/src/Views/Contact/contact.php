<h1>Formulaire de contact</h1>
<form action="?c=contact&a=enregistrer" method="post">
    <div class="mb-3">
        <label for= "nom" class="form-label">Votre nom</label>
        <input type="text" class="form-control"  name="nom" id="nom" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Votre email</label>
        <input type="email" class="form-control" name="email" id="email" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" name="description" id="desription" rows=3 required></textarea>
    </div>
    <div class="mb-3">
        <button type="submit" class="btn btn-primary" id="enregistrer" >Enregistrer</button>
    </div>
</form>
