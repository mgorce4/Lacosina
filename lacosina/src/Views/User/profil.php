<?php if (isset($_SESSION['user_id'])): ?>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <h1>Profil de l'utilisateur : <span id='profil_identifiant_titre'><?php echo htmlspecialchars($_SESSION['identifiant']); ?></span></h1>
            
            <div class="row mt-4">
                <div class="col-md-4 text-center">
                    <img class="w-75 rounded mx-auto img-fluid" src="<?php echo 'upload/profil.png';?>" alt="<?php echo htmlspecialchars($_SESSION['identifiant']);?>" onerror="this.src='upload/no-image.png'">
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Informations personnelles</h5>
                            <hr>
                            <p><b>Identifiant : </b><span id='profil_identifiant' data-id="<?php echo $_SESSION['user_id']; ?>" contenteditable="true"><?php echo htmlspecialchars($_SESSION['identifiant']); ?></span></p>
                            <p><b>Email : </b><span id='profil_mail' data-id="<?php echo $_SESSION['user_id']; ?>" contenteditable="true"><?php echo htmlspecialchars($_SESSION['mail']); ?></span></p>
                            <p><b>Statut : </b>
                                <?php if ($_SESSION['isAdmin'] == 1): ?>
                                    <span class="badge bg-danger">Administrateur</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Utilisateur</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            <div id="boutons" class="mt-4">
                <button id="bouton_modifier_profil" class="btn btn-warning d-none">Enregistrer les modifications</button>
                <a href="?c=home" class="btn btn-secondary">Retour à l'accueil</a>
                <a href="?c=ajout" class="btn btn-primary">Ajouter une recette</a>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning">
        Vous devez être connecté pour accéder à votre profil.
    </div>
    <a href="?c=User&a=connexion" class="btn btn-primary">Se connecter</a>
<?php endif; ?>