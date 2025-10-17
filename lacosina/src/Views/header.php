<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Cosina</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Script JavaScript personnalisé -->
    <script src="./src/Views/js/front.js" defer></script>
</head>
<body>
    <!-- menu de navigation -->
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href='?c=home'>Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='?c=lister'>Recettes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='?c=contact'>Contact</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php
                // Vérifier si l'utilisateur est connecté
                if (isset($_SESSION['user_id'])): ?>
                    <!-- Menu déroulant pour l'utilisateur connecté -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Bienvenue, <strong><?php echo htmlspecialchars($_SESSION['identifiant']); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href='?c=User&a=profil'>Mon profil</a></li>
                            <li><a class="dropdown-item" href='?c=ajout'>Ajouter une recette</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href='?c=User&a=deconnexion'>Déconnexion</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-primary me-2" href='?c=User&a=inscription'>Inscription</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary" href='?c=User&a=connexion'>Connexion</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- corps de la page -->
    <div class="container w-75 m-auto">
