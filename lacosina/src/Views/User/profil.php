<div class="row">
    <div class="col-md-8 offset-md-2">
        <h1>Mon profil</h1>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Informations personnelles</h5>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Identifiant :</strong></label>
                        <p><?php echo htmlspecialchars($_SESSION['identifiant']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Email :</strong></label>
                        <p><?php echo htmlspecialchars($_SESSION['mail']); ?></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Statut :</strong></label>
                        <p>
                            <?php if ($_SESSION['isAdmin'] == 1): ?>
                                <span class="badge bg-danger">Administrateur</span>
                            <?php else: ?>
                                <span class="badge bg-primary">Utilisateur</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <a href="?c=home" class="btn btn-secondary">Retour à l'accueil</a>
                <a href="?c=ajout" class="btn btn-primary">Ajouter une recette</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                Vous devez être connecté pour accéder à votre profil.
            </div>
            <a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>
        <?php endif; ?>
    </div>
</div>