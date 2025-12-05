<div class="container mt-4">
    <h1 class="mb-4">
        <i class="bi bi-clock-history"></i> Recettes en attente d'approbation
    </h1>

    <?php if (empty($recettesEnAttente)): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Aucune recette en attente d'approbation.
        </div>
    <?php else: ?>
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong><?php echo count($recettesEnAttente); ?></strong> recette(s) en attente d'approbation.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Image</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Type</th>
                        <th>Date de création</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recettesEnAttente as $recette): ?>
                        <tr>
                            <td>
                                <img src="<?php echo htmlspecialchars($recette['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($recette['titre']); ?>" 
                                     class="img-thumbnail" 
                                     style="max-width: 80px; max-height: 80px; object-fit: cover;">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($recette['titre']); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <?php echo htmlspecialchars(substr($recette['description'], 0, 100)); ?>
                                    <?php echo strlen($recette['description']) > 100 ? '...' : ''; ?>
                                </small>
                            </td>
                            <td><?php echo htmlspecialchars($recette['auteur']); ?></td>
                            <td>
                                <?php
                                $badges = [
                                    'entree' => 'bg-success',
                                    'plat' => 'bg-primary',
                                    'dessert' => 'bg-warning'
                                ];
                                $types = [
                                    'entree' => 'Entrée',
                                    'plat' => 'Plat',
                                    'dessert' => 'Dessert'
                                ];
                                ?>
                                <span class="badge <?php echo $badges[$recette['type_plat']]; ?>">
                                    <?php echo $types[$recette['type_plat']]; ?>
                                </span>
                            </td>
                            <td>
                                <small><?php echo date('d/m/Y H:i', strtotime($recette['date_creation'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?c=Recette&a=detail&id=<?php echo $recette['id']; ?>" 
                                       class="btn btn-sm btn-info" 
                                       title="Voir les détails">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="?c=Recette&a=approuver&id=<?php echo $recette['id']; ?>" 
                                       class="btn btn-sm btn-success" 
                                       title="Approuver"
                                       onclick="return confirm('Êtes-vous sûr de vouloir approuver cette recette ?');">
                                        <i class="bi bi-check-circle"></i> Approuver
                                    </a>
                                    <a href="?c=Recette&a=supprimer&id=<?php echo $recette['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette recette ?');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
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
