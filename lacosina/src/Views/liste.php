<body>

    <h1>Recettes</h1>

    <div>
        <?php foreach ($recipes as $recipe) : ?>
            <div>
                <h2><?php echo $recipe['titre']; ?></h2>
                <p><?php echo $recipe['description']; ?></p>
                <a href="mailto:<?php echo $recipe['auteur']; ?>"><?php echo $recipe['auteur']; ?></a>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="?c=home" class="btn btn-primary">Retour à l'accueil</a>
</body>