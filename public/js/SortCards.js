document.addEventListener('DOMContentLoaded', function () {
    const sortCategory = document.getElementById('sortCategory');
    const cardContainer = document.querySelector('.row.justify-content-center');

    sortCategory.addEventListener('change', function () {
        const selectedCategory = this.value;
        filterCardsByCategory(selectedCategory);
    });

    function filterCardsByCategory(category) {
        const cards = Array.from(cardContainer.children);

        cards.forEach(card => {
            const cardCategory = card.querySelector('.card-subtitle').textContent;
            if (category === 'default' || cardCategory === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
});