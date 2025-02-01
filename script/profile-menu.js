document.addEventListener('DOMContentLoaded', function () {
    const profileIcon = document.querySelector('.profile-icon');
    const dropdownMenu = document.querySelector('.dropdown-menu');

    // Mostra il menu quando si passa il mouse sull'icona del profilo
    profileIcon.addEventListener('mouseenter', function () {
        dropdownMenu.style.display = 'block';
    });

    // Nascondi il menu quando si clicca fuori dal menu o sull'icona del profilo
    document.addEventListener('click', function (event) {
        if (!profileIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
            dropdownMenu.style.display = 'none';
        }
    });

    // Nascondi il menu quando si scorre la pagina
    document.addEventListener('scroll', function () {
        dropdownMenu.style.display = 'none';
    });
});