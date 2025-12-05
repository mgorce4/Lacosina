<div class="container mt-4">
    <h1 class="mb-4">
        <i class="bi bi-clock-history"></i> Mes recettes en cours de validation
    </h1>

    <?php if (empty($recettesEnCours)): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Vous n'avez aucune recette en attente de validation.
        </div>
        <p>Toutes vos recettes proposées ont été validées ou vous n'avez pas encore proposé de recette.</p>
        <a href="?c=ajout" class="btn btn-primary mt-3">
            <i class="bi bi-plus-circle"></i> Proposer une nouvelle recette
        </a>
    <?php else: ?>
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-hourglass-split"></i> 
            Vous avez <strong><?php echo count($recettesEnCours); ?></strong> recette(s) en cours de validation par un administrateur.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-primary">
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Date de création</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recettesEnCours as $recette): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($recette['titre']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(substr($recette['description'], 0, 150)); ?>
                                <?php echo strlen($recette['description']) > 150 ? '...' : ''; ?>
                            </td>
                            <td>
                                <small><?php echo date('d/m/Y H:i', strtotime($recette['date_creation'])); ?></small>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="?c=ajout" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Proposer une autre recette
            </a>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="?c=home" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour à l'accueil
        </a>
    </div>
</div>
