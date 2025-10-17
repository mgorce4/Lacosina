<div class="row">
    <div class="col-md-6 offset-md-3">
        <h1>Connexion</h1>
        <form action="?c=User&a=verifieConnexion" method="post">
            <div class="mb-3">
                <label for="identifiant" class="form-label">Identifiant</label>
                <input type="text" class="form-control" name="identifiant" id="identifiant" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-primary" id="connexion">Se connecter</button>
                <a href="?c=User&a=inscription" class="btn btn-secondary">Pas encore inscrit ? S'inscrire</a>
            </div>
        </form>
    </div>
</div>