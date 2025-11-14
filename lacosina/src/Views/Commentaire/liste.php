<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="bi bi-chat-left-text"></i> Liste des commentaires
        </h1>
        
        <?php 
        // Récupérer tous les commentaires avec les informations de la recette
        $commentaires = $this->commentaireModel->findAll();
        
        if (!empty($commentaires)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Pseudo</th>
                            <th>Commentaire</th>
                            <th>Recette</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($commentaires as $commentaire): 
                            // Récupérer les informations de la recette
                            require_once(__DIR__ . '/../../Models/Recette.php');
                            $recetteModel = new Recette();
                            $recette = $recetteModel->find($commentaire['recette_id']);
                        ?>
                            <tr>
                                <td>
                                    <?php echo date('d/m/Y à H:i', strtotime($commentaire['create_time'])); ?>
                                </td>
                                <td>
                                    <i class="bi bi-person-circle"></i> 
                                    <?php echo htmlspecialchars($commentaire['pseudo']); ?>
                                </td>
                                <td>
                                    <?php 
                                    $texte = htmlspecialchars($commentaire['commentaire']);
                                    echo strlen($texte) > 100 ? substr($texte, 0, 100) . '...' : $texte;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($recette): ?>
                                        <a href="?c=Recette&a=detail&id=<?php echo $recette['id']; ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($recette['titre']); ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Recette supprimée</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="?c=Commentaire&a=supprimer&id=<?php echo $commentaire['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?');">
                                        <i class="bi bi-trash"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> 
                Total : <?php echo count($commentaires); ?> commentaire(s)
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                Aucun commentaire pour le moment.
            </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="?c=home" class="btn btn-primary">
                <i class="bi bi-house"></i> Retour à l'accueil
            </a>
        </div>
    </div>
</div>
