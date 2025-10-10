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
            event.preventDefault(); //empêche le comportement par défaut
            let recipeId = recipe.dataset.id; //Récupère l'id de la recette
            
            // Ouvrir la vue de détail avec window.open
            window.open(`index.php?c=Recette&a=detail&id=${recipeId}`, '_blank');
        })
    })
});