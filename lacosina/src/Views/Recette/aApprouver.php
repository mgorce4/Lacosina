<div class="container mt-4">
    <h1 class="mb-4">
        <i class="bi bi-clock-history"></i> Recettes à approuver
    </h1>

    <?php if (empty($recettesEnAttente)): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Aucune recette à approuver.
        </div>
    <?php else: ?>
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong><?php echo count($recettesEnAttente); ?></strong> recette(s) à approuver.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Titre</th>
                        <th>Description</th>
                        <th>Auteur</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recettesEnAttente as $recette): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($recette['titre']); ?></strong>
                            </td>
                            <td>
                                <?php echo htmlspecialchars(substr($recette['description'], 0, 150)); ?>
                                <?php echo strlen($recette['description']) > 150 ? '...' : ''; ?>
                            </td>
                            <td><?php echo htmlspecialchars($recette['auteur']); ?></td>
                            <td>
                                <a href="?c=Recette&a=valider&id=<?php echo $recette['id']; ?>" 
                                   class="btn btn-sm btn-success" 
                                   title="Valider"
                                   onclick="return confirm('Êtes-vous sûr de vouloir valider cette recette ?');">
                                    <i class="bi bi-check-circle"></i> Valider
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="?c=Recette&a=index" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste des recettes
        </a>
    </div>
</div>
