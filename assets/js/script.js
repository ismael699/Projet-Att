// Changement de l'input du formulaire register
document.addEventListener('DOMContentLoaded', function() {
    const roleInputs = document.querySelectorAll('input[name="user[roles]"]');
    const submitButton = document.getElementById('submit-button');

    const prices = {
        'ROLE_CLIENT': 'Payer 10,00 €',
        'ROLE_CHAUFFEUR': 'Payer 15,00 €'
    };

    roleInputs.forEach(input => {
        input.addEventListener('change', function() {
            const selectedRole = this.value;
            submitButton.textContent = prices[selectedRole];
        });
    });
});



// Menu déroulant index du profil 
document.addEventListener('DOMContentLoaded', function() {
    function toggleMenu(toggleButtonSelector, menuContentSelector, arrowSelector, firstArrow, secondArrow) {
        const menuToggle = document.querySelector(toggleButtonSelector);
        const menuContent = document.querySelector(menuContentSelector);
        const arrow = document.querySelector(arrowSelector);

        menuToggle.addEventListener('click', function() {
            menuContent.classList.toggle('open');
            if (menuContent.classList.contains('open')) {
                arrow.innerHTML = secondArrow;
            } else {
                arrow.innerHTML = firstArrow;
            }
        });
    }

    const firstArrow = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    `;

    const secondArrow = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
        </svg>
    `;

    // Index profil
    toggleMenu('.first-button-infos', '.profile-menu-contenu', '.arrow-info', firstArrow, secondArrow);

    toggleMenu('.second-button-infos', '.second-profile-menu-contenu', '.second-arrow-info', firstArrow, secondArrow);

    toggleMenu('.three-button-infos', '.three-profile-menu-contenu', '.three-arrow-info', firstArrow, secondArrow);

    toggleMenu('.four-button-infos', '.four-profile-menu-contenu', '.four-arrow-info', firstArrow, secondArrow);

    toggleMenu('.five-button-infos', '.five-profile-menu-contenu', '.five-arrow-info', firstArrow, secondArrow);

    toggleMenu('.six-button-infos', '.six-profile-menu-contenu', '.six-arrow-info', firstArrow, secondArrow);
});




// Index annonces
document.addEventListener('DOMContentLoaded', function() {
    function toggleMenu(button, menu, arrow, firstArrow, secondArrow) {
        button.addEventListener('click', function() {
            menu.classList.toggle('open');
            if (menu.classList.contains('open')) {
                arrow.innerHTML = secondArrow;
            } else {
                arrow.innerHTML = firstArrow;
            }
        });
    }

    const firstArrow = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    `;

    const secondArrow = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
        </svg>
    `;

    // Gestion du formulaire de recherche
    const searchButton = document.querySelector('.annonce-button-infos');
    const searchMenu = document.querySelector('.first-annonce-search');
    const searchArrow = document.querySelector('.search-arrow');
    toggleMenu(searchButton, searchMenu, searchArrow, firstArrow, secondArrow);

    // Gestion des annonces
    const annonceButtons = document.querySelectorAll('.annonce-second-button-infos');
    annonceButtons.forEach(button => {
        const menu = button.nextElementSibling;
        const arrow = button.querySelector('.annonce-arrow');
        toggleMenu(button, menu, arrow, firstArrow, secondArrow);
    });
});