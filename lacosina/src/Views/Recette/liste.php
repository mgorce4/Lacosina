<div class="row">
    <?php foreach ($recettes as $recipe) :?>
        <div class="col-4 p-2">
            <div class="recipe card" data-id="<?php echo $recipe['id']; ?>">
                <?php 
                // Gestion de l'affichage de l'image
                $imageSrc = !empty($recipe['image']) ? htmlspecialchars($recipe['image']) : 'upload/no-image.png';
                ?>
                <img src="<?php echo $imageSrc; ?>" class="card-img-top" alt="Image de <?php echo htmlspecialchars($recipe['titre']); ?>" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h2 class="card-title"><?php echo htmlspecialchars($recipe['titre']); ?></h2>
                    <p class="card-text"><?php echo htmlspecialchars(substr($recipe['description'], 0, 100)) . '...'; ?></p>
                    <p class="text-muted">Auteur : <a href="mailto:<?php echo htmlspecialchars($recipe['auteur']); ?>"><?php echo htmlspecialchars($recipe['auteur']); ?></a></p>
                    <div class="mt-3">
                        <a href="?c=Recette&a=detail&id=<?php echo $recipe['id']; ?>" class="btn btn-primary btn-sm">
                            Voir la recette complète
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>