//Écoute le chargement du DOM
document.addEventListener('DOMContentLoaded', () => {

    // Sélectionner les champs modifiables du profil
    let identifiantField = document.querySelector('#profil_identifiant');
    let mailField = document.querySelector('#profil_mail');
    let boutonModifier = document.querySelector('#bouton_modifier_profil');

    // Vérifier que les éléments existent (on est sur la page profil)
    if (identifiantField && mailField && boutonModifier) {
        
        // Stocker les valeurs initiales
        let initialIdentifiant = identifiantField.textContent;
        let initialMail = mailField.textContent;

        // Fonction pour vérifier si les champs ont été modifiés
        function checkForChanges() {
            let currentIdentifiant = identifiantField.textContent;
            let currentMail = mailField.textContent;

            // Si l'identifiant ou le mail a changé, afficher le bouton
            if (currentIdentifiant !== initialIdentifiant || currentMail !== initialMail) {
                boutonModifier.classList.remove('d-none');
            } else {
                boutonModifier.classList.add('d-none');
            }
        }

        // Ajouter des écouteurs d'événements sur les champs
        identifiantField.addEventListener('input', checkForChanges);
        mailField.addEventListener('input', checkForChanges);

        // Optionnel : gérer le clic sur le bouton de modification
        boutonModifier.addEventListener('click', () => {
            // Récupérer les nouvelles valeurs
            let newIdentifiant = identifiantField.textContent.trim();
            let newMail = mailField.textContent.trim();
            let userId = identifiantField.dataset.id;

            // Validation basique
            if (newIdentifiant === '' || newMail === '') {
                alert('L\'identifiant et l\'email ne peuvent pas être vides.');
                return;
            }

            // Validation email
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(newMail)) {
                alert('Veuillez entrer une adresse email valide.');
                return;
            }

            // Désactiver le bouton pendant l'envoi
            boutonModifier.disabled = true;
            boutonModifier.textContent = 'Enregistrement en cours...';

            // Envoyer les données au serveur via fetch
            fetch('index.php?c=User&a=modifierProfil', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: userId,
                    identifiant: newIdentifiant,
                    mail: newMail
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Succès : mettre à jour les valeurs initiales
                    initialIdentifiant = newIdentifiant;
                    initialMail = newMail;
                    
                    // Masquer le bouton
                    boutonModifier.classList.add('d-none');
                    
                    // Mettre à jour le titre de la page
                    let titreProfil = document.querySelector('#profil_identifiant_titre');
                    if (titreProfil) {
                        titreProfil.textContent = newIdentifiant;
                    }
                    
                    // Mettre à jour le menu de navigation si présent
                    let menuIdentifiant = document.querySelector('.dropdown-toggle strong');
                    if (menuIdentifiant) {
                        menuIdentifiant.textContent = newIdentifiant;
                    }
                    
                    // Afficher un message de succès
                    showAlert('success', data.message);
                } else {
                    // Erreur : afficher le message
                    showAlert('danger', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                showAlert('danger', 'Une erreur est survenue lors de la mise à jour du profil.');
            })
            .finally(() => {
                // Réactiver le bouton
                boutonModifier.disabled = false;
                boutonModifier.textContent = 'Enregistrer les modifications';
            });
        });
    }

    // Fonction pour afficher des alertes Bootstrap
    function showAlert(type, message) {
        // Créer l'élément d'alerte
        let alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insérer l'alerte au début du container
        let container = document.querySelector('.container');
        if (container) {
            container.insertBefore(alertDiv, container.firstChild);
            
            // Faire disparaître l'alerte après 5 secondes
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 5000);
        }
    }

    // ========== Gestion de la liste des favoris ==========
    
    // Récupérer la balise div qui listera les recettes favorites
    let listeFavoris = document.querySelector('#liste-favoris');
    
    // Si cette div existe, récupérer la liste des favoris via fetch
    if (listeFavoris) {
        // Récupérer l'ID de l'utilisateur via l'attribut data-id
        let userId = listeFavoris.dataset.id;
        
        // Fonction pour charger les favoris via fetch
        function chargerFavoris() {
            fetch(`index.php?c=Favori&a=getFavoris`)
            .then(response => response.json()) // Maintenant on peut parser directement en JSON
            .then(data => {
                if (data.success) {
                    console.log('Favoris chargés:', data);
                    console.log('Nombre de favoris:', data.count);
                    
                    // Afficher les favoris sous forme de liste à puces
                    afficherFavorisEnListe(data.favoris);
                } else {
                    console.error('Erreur:', data.message);
                }
            })
            .catch(error => {
                console.error('Erreur lors du chargement des favoris:', error);
            });
        }
        
        // Fonction pour afficher les favoris sous forme de liste à puces
        function afficherFavorisEnListe(favoris) {
            // Créer une div pour la liste
            let listeDiv = document.createElement('div');
            listeDiv.className = 'mt-3';
            listeDiv.innerHTML = '<h4>Liste des favoris (format liste) :</h4>';
            
            if (favoris.length === 0) {
                listeDiv.innerHTML += '<p class="text-muted">Aucun favori pour le moment.</p>';
            } else {
                // Créer une liste à puces
                let ul = document.createElement('ul');
                ul.className = 'list-group';
                
                favoris.forEach(favori => {
                    let li = document.createElement('li');
                    li.className = 'list-group-item';
                    li.innerHTML = `
                        <strong>${favori.titre}</strong> par ${favori.auteur}
                        <br>
                        <small class="text-muted">${favori.description.substring(0, 100)}...</small>
                        <br>
                        <a href="?c=Recette&a=detail&id=${favori.recette_id}" class="btn btn-sm btn-primary mt-2">
                            <i class="bi bi-eye"></i> Voir la recette
                        </a>
                    `;
                    ul.appendChild(li);
                });
                
                listeDiv.appendChild(ul);
            }
            
            // Ajouter la liste au DOM (après le titre)
            let titre = document.querySelector('h1');
            if (titre && titre.nextSibling) {
                titre.parentNode.insertBefore(listeDiv, titre.nextSibling);
            }
        }
        
        // Décommenter pour charger les favoris automatiquement au chargement de la page
        // chargerFavoris();
    }

    // ===== NOTIFICATIONS POUR LES RECETTES À APPROUVER (ADMIN UNIQUEMENT) =====
    // Fonction pour afficher les notifications de recettes en attente
    async function afficherNotificationsAdmin() {
        try {
            // Appeler l'API pour obtenir le nombre de recettes non validées
            const response = await fetch('?c=Recette&a=compterNonValidees');
            const data = await response.json();
            
            const count = data.count || 0;
            
            // Afficher le badge dans le menu
            if (count > 0) {
                const badgeMenu = document.getElementById('badge-recettes-menu');
                if (badgeMenu) {
                    badgeMenu.textContent = count;
                    badgeMenu.style.display = 'inline-block';
                }
            }
        } catch (error) {
            console.error('Erreur lors du chargement des notifications de recettes:', error);
        }
    }

    // ===== NOTIFICATIONS POUR LES COMMENTAIRES À APPROUVER (ADMIN UNIQUEMENT) =====
    // Fonction pour afficher les notifications de commentaires en attente
    async function afficherNotificationsCommentaires() {
        try {
            // Appeler l'API pour obtenir le nombre de commentaires non validés
            const response = await fetch('?c=Commentaire&a=compterNonValidees');
            const data = await response.json();
            
            const count = data.count || 0;
            
            // Afficher le badge dans le menu
            if (count > 0) {
                const badgeMenu = document.getElementById('badge-commentaires-menu');
                if (badgeMenu) {
                    badgeMenu.textContent = count;
                    badgeMenu.style.display = 'inline-block';
                }
            }
        } catch (error) {
            console.error('Erreur lors du chargement des notifications de commentaires:', error);
        }
    }

    // ===== BADGE TOTAL SUR LE PROFIL ADMIN =====
    // Fonction pour afficher le badge total sur le bouton du profil admin
    async function afficherBadgeTotalAdmin() {
        try {
            // Récupérer les deux compteurs en parallèle
            const [recettesResponse, commentairesResponse] = await Promise.all([
                fetch('?c=Recette&a=compterNonValidees'),
                fetch('?c=Commentaire&a=compterNonValidees')
            ]);
            
            const recettesData = await recettesResponse.json();
            const commentairesData = await commentairesResponse.json();
            
            const totalCount = (recettesData.count || 0) + (commentairesData.count || 0);
            
            // Afficher le badge sur le bouton du profil (juste un point rouge sans chiffre)
            if (totalCount > 0) {
                const badgeProfil = document.getElementById('badge-profil-admin');
                if (badgeProfil) {
                    badgeProfil.style.display = 'inline-block';
                }
            }
        } catch (error) {
            console.error('Erreur lors du chargement du badge total admin:', error);
        }
    }

    // Vérifier si l'utilisateur est admin en vérifiant l'existence du badge profil admin
    const badgeProfilAdmin = document.getElementById('badge-profil-admin');
    if (badgeProfilAdmin) {
        // L'utilisateur est admin, charger les notifications
        afficherNotificationsAdmin();
        afficherNotificationsCommentaires();
        afficherBadgeTotalAdmin();
    }
});

