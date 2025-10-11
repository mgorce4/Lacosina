<?php if (isset($recette) && $recette): ?>
    <!-- DEBUG: Recette trouvée, ID = <?php echo $recette['id']; ?> -->
    <div class="row">
        <div class="col-12">
            <h1><?php echo htmlspecialchars($recette['titre']); ?></h1>
            
            <?php 
            // Gestion de l'affichage de l'image
            $imageSrc = !empty($recette['image']) ? htmlspecialchars($recette['image']) : 'upload/no-image.png';
            ?>
            <div class="text-center mb-4">
                <img src="<?php echo $imageSrc; ?>" class="img-fluid rounded" alt="Image de <?php echo htmlspecialchars($recette['titre']); ?>" style="max-height: 400px; object-fit: cover;">
            </div>
            
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($recette['description'])); ?></p>
                    
                    <hr>
                    
                    <p class="text-muted">
                        <strong>Auteur :</strong> 
                        <a href="mailto:<?php echo htmlspecialchars($recette['auteur']); ?>" class="text-decoration-none">
                            <?php echo htmlspecialchars($recette['auteur']); ?>
                        </a>
                    </p>
                    
                    <?php if (isset($recette['date_creation'])): ?>
                        <p class="text-muted">
                            <strong>Date de création :</strong> 
                            <?php echo date('d/m/Y à H:i', strtotime($recette['date_creation'])); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-4 mb-5">
                <div class="row">
                    <div class="col-12">
                        <a href="?c=Recette&a=modifier&id=<?php echo $recette['id']; ?>" class="btn btn-primary">
                            Modifier la recette
                        </a>
                        <a href="?c=Recette&a=lister" class="btn btn-primary me-3">
                            Retour à la liste des recettes
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning" role="alert">
        <h4 class="alert-heading">Recette non trouvée</h4>
        <p>La recette demandée n'existe pas ou n'a pas pu être chargée.</p>
        <hr>
        <p class="mb-0">
            <a href="?c=Recette&a=lister" class="btn btn-primary">Retour à la liste des recettes</a>
        </p>
    </div>
<?php endif; ?>