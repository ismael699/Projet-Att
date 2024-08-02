// Menu déroulant _formSearch 

document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.querySelector('.menu-toggle');
    const menuContent = document.querySelector('.menu-content');
    const arrow = document.querySelector('.arrow');

    const downArrow = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
        </svg>
    `;

    const upArrow = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
        </svg>
    `;

    menuToggle.addEventListener('click', function() {
        menuContent.classList.toggle('open');
        if (menuContent.classList.contains('open')) {
            arrow.innerHTML = upArrow;
        } else {
            arrow.innerHTML = downArrow;
        }
    });
});


// Menu déroulant profile

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

    // Appel de la fonction 
    toggleMenu('.profile-button-infos', '.profile-menu-contenu', '.arrow-info', firstArrow, secondArrow);

    toggleMenu('.second-button-infos', '.second-profile-menu-contenu', '.second-arrow-info', firstArrow, secondArrow);

    toggleMenu('.three-button-infos', '.three-profile-menu-contenu', '.three-arrow-info', firstArrow, secondArrow);

    toggleMenu('.four-button-infos', '.four-profile-menu-contenu', '.four-arrow-info', firstArrow, secondArrow);

    toggleMenu('.five-button-infos', '.five-profile-menu-contenu', '.five-arrow-info', firstArrow, secondArrow);

    toggleMenu('.six-button-infos', '.six-profile-menu-contenu', '.six-arrow-info', firstArrow, secondArrow);
});



