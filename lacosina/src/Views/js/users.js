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
});
