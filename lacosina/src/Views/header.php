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
    <!-- Scripts JavaScript personnalisés -->
    <script src="src/Views/js/recipes.js" defer></script>
    <script src="src/Views/js/users.js" defer></script>
    <script src="src/Views/js/search.js" defer></script>
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
                    <a class="nav-link" href='?c=Recette&a=index'>Recettes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href='?c=contact'>Contact</a>
                </li>
            </ul>
            <!-- Zone de recherche -->
            <form class="d-flex me-3" role="search">
                <input class="form-control" type="search" id="search" placeholder="Rechercher une recette..." aria-label="Search">
            </form>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php
                // Vérifier si l'utilisateur est connecté
                if (isset($_SESSION['user_id'])): ?>
                    <!-- Menu déroulant pour l'utilisateur connecté -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                Bienvenue <strong>Administrateur (<?php echo htmlspecialchars($_SESSION['identifiant']); ?>)</strong>
                                <span id="badge-profil-admin" class="position-absolute badge rounded-circle bg-danger" style="display: none !important; width: 12px; height: 12px; padding: 0; top: 5px; right: -5px;">
                                </span>
                            <?php else: ?>
                                Bienvenue <strong><?php echo htmlspecialchars($_SESSION['identifiant']); ?></strong>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href='?c=User&a=profil'>Mon profil</a></li>
                            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                <li><a class="dropdown-item" href='?c=ajout'>Ajouter une recette</a></li>
                            <?php else: ?>
                                <li><a class="dropdown-item" href='?c=ajout'>Proposer une recette</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href='?c=Favori&a=liste'>Mes recettes favorites</a></li>
                            <?php if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] != 1): ?>
                                <li><a class="dropdown-item" href='?c=Recette&a=nonValidesPourUtilisateur'>Mes recettes en cours de validation</a></li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 1): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><h6 class="dropdown-header">Administration</h6></li>
                                <li><a class="dropdown-item d-flex justify-content-between align-items-center" href='?c=Recette&a=aApprouver' id="menu-recettes-approuver">
                                    Recettes à approuver
                                    <span id="badge-recettes-menu" class="badge bg-danger rounded-pill" style="display: none;">0</span>
                                </a></li>
                                <li><a class="dropdown-item d-flex justify-content-between align-items-center" href='?c=Commentaire&a=aApprouver' id="menu-commentaires-approuver">
                                    Commentaires à approuver
                                    <span id="badge-commentaires-menu" class="badge bg-danger rounded-pill" style="display: none;">0</span>
                                </a></li>
                                <li><a class="dropdown-item" href='?c=Commentaire&a=liste'>Liste des commentaires</a></li>
                            <?php endif; ?>
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
        
        <?php
        // Afficher les messages de la session
        if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo htmlspecialchars($_SESSION['message_type'] ?? 'info'); ?> alert-dismissible fade show mt-3" role="alert">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            // Supprimer le message après l'avoir affiché
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif;
        ?>
