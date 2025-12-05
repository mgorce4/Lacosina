<div class="container mt-4">
    <h1 class="mb-4">
        <i class="bi bi-chat-left-text"></i> Commentaires à approuver
    </h1>

    <?php if (empty($commentairesEnAttente)): ?>
        <div class="alert alert-info" role="alert">
            <i class="bi bi-info-circle"></i> Aucun commentaire à approuver.
        </div>
    <?php else: ?>
        <div class="alert alert-warning mb-4" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            <strong><?php echo count($commentairesEnAttente); ?></strong> commentaire(s) à approuver.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Recette</th>
                        <th>Pseudo</th>
                        <th>Commentaire</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($commentairesEnAttente as $commentaire): ?>
                        <tr>
                            <td>
                                <a href="?c=Recette&a=detail&id=<?php echo $commentaire['recette_id']; ?>" target="_blank">
                                    <?php echo htmlspecialchars($commentaire['recette_titre'] ?? 'Recette #' . $commentaire['recette_id']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($commentaire['pseudo']); ?></td>
                            <td>
                                <?php echo htmlspecialchars(substr($commentaire['commentaire'], 0, 100)); ?>
                                <?php echo strlen($commentaire['commentaire']) > 100 ? '...' : ''; ?>
                            </td>
                            <td>
                                <small><?php echo date('d/m/Y H:i', strtotime($commentaire['create_time'])); ?></small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?c=Commentaire&a=approuver&id=<?php echo $commentaire['id']; ?>" 
                                       class="btn btn-sm btn-success" 
                                       title="Approuver"
                                       onclick="return confirm('Êtes-vous sûr de vouloir approuver ce commentaire ?');">
                                        <i class="bi bi-check-circle"></i> Approuver
                                    </a>
                                    <a href="?c=Commentaire&a=supprimer&id=<?php echo $commentaire['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?');">
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
        <a href="?c=Commentaire&a=liste" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Retour à la liste des commentaires
        </a>
    </div>
</div>
