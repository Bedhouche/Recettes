document.addEventListener('DOMContentLoaded', function() {
    var categorySelect = document.querySelector('.category-search');

    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            var selectedCategories = this.selectedOptions;
            var selectedCategoriesContainer = document.querySelector('.selected-categories');

            // Effacer le contenu précédent
            selectedCategoriesContainer.innerHTML = '';

            // Ajouter chaque catégorie sélectionnée avec une icône de suppression
            selectedCategories.forEach(function(option) {
                var categoryName = option.textContent.trim();
                var categoryId = option.value;

                var categoryTag = document.createElement('div');
                categoryTag.classList.add('selected-category');

                var categoryNameSpan = document.createElement('span');
                categoryNameSpan.textContent = categoryName;

                var categoryRemoveIcon = document.createElement('span');
                categoryRemoveIcon.textContent = 'x';
                categoryRemoveIcon.classList.add('remove-category');
                categoryRemoveIcon.dataset.categoryId = categoryId;

                categoryTag.appendChild(categoryNameSpan);
                categoryTag.appendChild(categoryRemoveIcon);

                selectedCategoriesContainer.appendChild(categoryTag);
            });
        });

        // Gérer la suppression d'une catégorie sélectionnée
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-category')) {
                var categoryId = event.target.dataset.categoryId;
                var categoryOption = document.querySelector('.category-search option[value="' + categoryId + '"]');
                if (categoryOption) {
                    categoryOption.selected = false;
                    var changeEvent = new Event('change');
                    categorySelect.dispatchEvent(changeEvent);
                }
            }
        });
    }
});
