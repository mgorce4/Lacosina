//ecoute le chargement de DOM
document.addEventListener('DOMContentLoaded', () => {

    //selectionne toutes les recettes avec la classe 'recipe
    let recipes = document.querySelectorAll('.recipe');

    //Ajouté un écouteur d'événements sur chaque recette
    recipes.forEach(recipe => {

        // Transformer le curseur de la souris en pointer
        recipe.style.cursor = 'pointer';

        recipe.addEventListener('mouseover', (event)=>{
            recipe.style.backgroundColor = 'lightgray'; //ajoute un fond gris lorsque la souris passe dessus la recette
        });

        recipe.addEventListener('mouseout', (event)=>{
            recipe.style.backgroundColor = ''; //retire le fond gris lorsque la souris sort de la recette
        });

        recipe.addEventListener('click', (event)=>{
            // Éviter la navigation si on clique sur un lien ou bouton existant
            if (event.target.tagName === 'A' || event.target.tagName === 'BUTTON' || event.target.closest('a') || event.target.closest('button')) {
                return; // Laisser le comportement normal des liens/boutons
            }
            
            let recipeId = recipe.dataset.id; //Récupère l'id de la recette
            
            if (recipeId) {
                // Naviguer vers la vue de détail dans la même fenêtre
                window.location.href = `index.php?c=Recette&a=detail&id=${recipeId}`;
            }
        })
    })
});