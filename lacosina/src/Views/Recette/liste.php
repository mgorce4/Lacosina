<div class="row">
    <?php foreach ($recettes as $recipe) :?>
        <div class="col-4 p-2">
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title"><?php echo $recipe['titre']; ?></h2>
                    <p class="card-text"><?php echo $recipe['description']; ?></p>
                    <p class="text-muted">Auteur : <a href="mailto:<?php echo $recipe['auteur']; ?>"><?php echo $recipe['auteur']; ?></a></p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>