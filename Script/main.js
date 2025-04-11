document.addEventListener('DOMContentLoaded', function() {
    // Обробка додавання/видалення з обраного
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('favorite-btn')) {
            const recipeId = e.target.getAttribute('data-id');
            toggleFavorite(recipeId, e.target);
        }
        if (e.target.closest('.favorites-remove-btn')) {
            const button = e.target.closest('.favorites-remove-btn');
            const recipeId = button.getAttribute('data-id');
            removeFromFavorites(recipeId, button);
        }
    });

    // Обробка фільтрів
    const filterForm = document.querySelector('.filters');
    if (filterForm) {
        const inputs = filterForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('change', applyFilters);
        });
    }
});

function toggleFavorite(recipeId, button) {
    fetch('Script/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'recipe_id=' + recipeId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = data.is_favorite ? '❤️' : '🤍';
            
            // Находим или создаём счётчик
            let counter = document.querySelector('.favorite-counter');
            const heartIcon = document.querySelector('.image__basket img');
            
            if (!counter && data.is_favorite) {
                counter = document.createElement('span');
                counter.className = 'favorite-counter';
                document.querySelector('.image__basket').appendChild(counter);
            }
            
            if (counter) {
                // Обновляем количество
                const currentCount = parseInt(counter.textContent || 0);
                counter.textContent = data.is_favorite ? currentCount + 1 : currentCount - 1;
                
                // Управляем видимостью
                if (parseInt(counter.textContent) <= 0) {
                    counter.remove(); // Удаляем если 0
                } else {
                    counter.style.display = 'flex';
                }
            }
            
            // Если мы на странице избранного - удаляем карточку при удалении
            if (window.location.pathname.includes('favorite.php') && !data.is_favorite) {
                button.closest('.recipe-card, .body__elem').remove();
                
                // Обновляем заголовок если список пуст
                if (document.querySelectorAll('.recipe-card, .body__elem').length === 0) {
                    document.querySelector('.favorites h1').insertAdjacentHTML('afterend', 
                        '<p>У вас поки що немає обраних рецептів</p>');
                }
            }
        }
    });
}

function applyFilters() {
    const minTime = document.querySelector('.filter__price__input:nth-child(1)').value;
    const maxTime = document.querySelector('.filter__price__input:nth-child(3)').value;
    const categories = Array.from(document.querySelectorAll('.filters__elem__checkboxes__checkbox input:checked'))
        .map(checkbox => checkbox.value);

    const formData = new FormData();
    formData.append('min_time', minTime);
    formData.append('max_time', maxTime);
    categories.forEach(cat => formData.append('categories[]', cat));

    fetch('Script/filter_recipes.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector('.content__body__elem').innerHTML = data.html;
        }
    });
}
function removeFromFavorites(recipeId, button) {
    fetch('Script/toggle_favorite.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'recipe_id=' + recipeId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && !data.is_favorite) {
            // Видаляємо картку рецепту
            const item = document.getElementById('favorite-' + recipeId);
            if (item) {
                item.style.transition = 'opacity 0.3s';
                item.style.opacity = '0';
                
                // Через 300ms (після завершення анімації) видаляємо елемент
                setTimeout(() => {
                    item.remove();
                    
                    // Перевіряємо, чи залишились ще улюблені рецепти
                    if (document.querySelectorAll('.favorites-item').length === 0) {
                        // Показуємо повідомлення про порожній список
                        const emptyMessage = document.createElement('div');
                        emptyMessage.className = 'favorites-empty';
                        emptyMessage.innerHTML = '<p>У вас поки що немає обраних рецептів</p>';
                        document.querySelector('.favorites-page').appendChild(emptyMessage);
                    }
                    
                    // Оновлюємо лічильник у шапці
                    updateFavoriteCounter(-1);
                }, 300);
            }
        }
    });
}

function updateFavoriteCounter(change) {
    let counter = document.querySelector('.favorite-counter');
    const heartIcon = document.querySelector('.image__basket img');
    
    if (!counter && change > 0) {
        counter = document.createElement('span');
        counter.className = 'favorite-counter';
        document.querySelector('.image__basket').appendChild(counter);
    }
    
    if (counter) {
        const currentCount = parseInt(counter.textContent || '0');
        const newCount = currentCount + change;
        
        if (newCount <= 0) {
            counter.remove();
        } else {
            counter.textContent = newCount;
            counter.style.display = 'flex';
        }
    }
}