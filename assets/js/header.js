document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuPrincipal = document.getElementById('menu-principal');
    
    // Crear el backdrop
    const backdrop = document.createElement('div');
    backdrop.className = 'menu-backdrop';
    document.body.appendChild(backdrop);

    let isMenuOpen = false;

    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        isMenuOpen = !isMenuOpen;
        toggleMenu(isMenuOpen);
    });

    // Función para manejar el toggle del menú
    function toggleMenu(show) {
        if (show) {
            menuToggle.classList.add('active');
            menuPrincipal.classList.add('active');
            backdrop.classList.add('active');
            // Eliminamos la manipulación del overflow
        } else {
            menuToggle.classList.remove('active');
            menuPrincipal.classList.remove('active');
            backdrop.classList.remove('active');
            // Eliminamos la manipulación del overflow
        }
        isMenuOpen = show;
    }

    // Cerrar el menú al hacer clic en un enlace
    const menuLinks = menuPrincipal.getElementsByTagName('a');
    Array.from(menuLinks).forEach(link => {
        link.addEventListener('click', () => toggleMenu(false));
    });

    // Cerrar el menú al hacer clic en el backdrop
    backdrop.addEventListener('click', () => toggleMenu(false));

    // Cerrar el menú al presionar ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMenuOpen) {
            toggleMenu(false);
        }
    });

    // Ajustar menú en cambio de tamaño de ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768 && isMenuOpen) {
            toggleMenu(false);
        }
    });

    // Añadir evento click al documento
    document.addEventListener('click', function(e) {
        // Si el menú está abierto y el clic no fue en el menú ni en el botón toggle
        if (isMenuOpen && 
            !menuPrincipal.contains(e.target) && 
            !menuToggle.contains(e.target)) {
            toggleMenu(false);
        }
    });

    // Prevenir que los clics dentro del menú cierren el menú
    menuPrincipal.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Prevenir que los clics en el botón toggle propaguen al documento
    menuToggle.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
