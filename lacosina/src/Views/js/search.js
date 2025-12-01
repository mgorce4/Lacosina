/**
 * Fichier search.js - Gestion de la recherche de recettes
 */

// Variable globale pour stocker les recettes
let recipes = [];

/**
 * Fonction pour charger toutes les recettes depuis le serveur
 */
async function loadRecipes() {
    try {
        const response = await fetch('?c=Recette&a=indexJSON');
        recipes = await response.json();
    } catch (error) {
        console.error('Erreur lors du chargement des recettes:', error);
        recipes = [];
    }
}

/**
 * Fonction pour filtrer les recettes en fonction de la recherche
 */
function filterRecipes() {
    // Récupérer la valeur de la zone de recherche
    const searchValue = document.getElementById('search').value.toLowerCase().trim();
    
    // Filtrer les recettes
    const filteredRecipes = recipes.filter(recipe => {
        return recipe.titre.toLowerCase().includes(searchValue);
    });
    
    // Afficher les recettes filtrées
    displayRecipes(filteredRecipes);
}

/**
 * Fonction pour afficher les recettes filtrées
 * @param {Array} recipesToDisplay - Tableau des recettes à afficher
 */
function displayRecipes(recipesToDisplay) {
    const resultsDiv = document.getElementById('results');
    
    // Vider la div pour rafraîchir l'affichage
    if (resultsDiv) {
        resultsDiv.innerHTML = '';
        
        // Vérifier s'il y a des résultats
        if (recipesToDisplay.length === 0) {
            resultsDiv.innerHTML = '<p class="text-muted">Aucune recette trouvée.</p>';
        } else {
            // Afficher chaque recette
            recipesToDisplay.forEach(recipe => {
                const recipeElement = document.createElement('div');
                recipeElement.className = 'mb-3 p-3 border rounded';
                recipeElement.innerHTML = `
                    <h5><a href="?c=Recette&a=detail&id=${recipe.id}" class="text-decoration-none">${recipe.titre}</a></h5>
                    <p class="text-muted mb-0">${recipe.description.substring(0, 150)}${recipe.description.length > 150 ? '...' : ''}</p>
                `;
                resultsDiv.appendChild(recipeElement);
            });
        }
    }
}

/**
 * Initialisation au chargement de la page
 */
document.addEventListener('DOMContentLoaded', async () => {
    // Charger toutes les recettes au démarrage
    await loadRecipes();
    
    const searchInput = document.getElementById('search');
    const container = document.querySelector('.container.w-75');
    
    if (searchInput && container) {
        // Au focus sur la zone de recherche
        searchInput.addEventListener('focus', () => {
            // Vider le conteneur et créer la structure de recherche
            container.innerHTML = '';
            
            const searchTitle = document.createElement('h1');
            searchTitle.textContent = 'Résultats de la recherche';
            searchTitle.className = 'mt-4 mb-3';
            container.appendChild(searchTitle);
            
            const resultsDiv = document.createElement('div');
            resultsDiv.id = 'results';
            resultsDiv.className = 'mt-3';
            container.appendChild(resultsDiv);
            
            // Lancer la recherche initiale
            filterRecipes();
        });
        
        // À la sortie du focus (blur)
        searchInput.addEventListener('blur', () => {
            // Attendre un peu avant de recharger pour permettre les clics sur les résultats
            setTimeout(() => {
                location.reload();
            }, 200);
        });
        
        // Lors de la saisie dans la zone de recherche
        searchInput.addEventListener('input', filterRecipes);
    }
});
