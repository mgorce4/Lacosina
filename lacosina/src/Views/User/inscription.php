<div class="row">
    <div class="col-md-6 offset-md-3">
        <h1>Inscription</h1>
        <form action="?c=User&a=enregistrer" method="post">
            <div class="mb-3">
                <label for="identifiant" class="form-label">Identifiant</label>
                <input type="text" class="form-control" name="identifiant" id="identifiant" required>
            </div>
            <div class="mb-3">
                <label for="mail" class="form-label">Adresse mail</label>
                <input type="email" class="form-control" name="mail" id="mail" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" id="password" required minlength="6">
                <div class="form-text">Le mot de passe doit contenir au moins 6 caractères.</div>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary" id="enregistrer">S'inscrire</button>
                <a href="?c=User&a=connexion" class="btn btn-secondary">Déjà inscrit ? Se connecter</a>
            </div>
        </form>
    </div>
</div>