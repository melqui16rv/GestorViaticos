document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menuPrincipal = document.getElementById('menu-principal');
    
    console.log('🚀 Navbar JS cargado - Panel Simple');
    console.log('MenuToggle encontrado:', !!menuToggle);
    console.log('MenuPrincipal encontrado:', !!menuPrincipal);
    
    if (!menuToggle || !menuPrincipal) {
        console.error('❌ Elementos del menú no encontrados');
        return;
    }

    let isMenuOpen = false;

    // Event listener para el botón hamburguesa
    menuToggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        isMenuOpen = !isMenuOpen;
        console.log('🔄 Toggle menú:', isMenuOpen ? 'ABRIR' : 'CERRAR');
        
        if (isMenuOpen) {
            openMenu();
        } else {
            closeMenu();
        }
    });

    // Función para abrir el menú - Panel simple
    function openMenu() {
        console.log('📱 Abriendo panel del menú...');
        
        // Activar estado visual
        menuToggle.classList.add('active');
        menuPrincipal.classList.add('active');
          // Prevenir scroll del body
        if (window.innerWidth <= 768) {
            document.body.style.overflow = 'hidden';
        }
        
        isMenuOpen = true;
        console.log('✅ Panel del menú abierto');
        logMenuState();
    }

    // Función para cerrar el menú - Panel simple
    function closeMenu() {
        console.log('📱 Cerrando panel del menú...');
        
        // Desactivar estado visual
        menuToggle.classList.remove('active');
        menuPrincipal.classList.remove('active');
        
        // Restaurar scroll del body
        document.body.style.overflow = '';
        
        isMenuOpen = false;
        console.log('✅ Panel del menú cerrado');
        logMenuState();
    }

    // Función para debug del estado del menú
    function logMenuState() {
        console.log('🔍 Estado actual:');
        console.log('  - isMenuOpen:', isMenuOpen);
        console.log('  - menuToggle.active:', menuToggle.classList.contains('active'));
        console.log('  - menuPrincipal.active:', menuPrincipal.classList.contains('active'));
        
        const style = window.getComputedStyle(menuPrincipal);
        console.log('  - opacity:', style.opacity);
        console.log('  - visibility:', style.visibility);
        console.log('  - transform:', style.transform);
        console.log('  - z-index:', style.zIndex);
    }

    // Event listeners para los enlaces del menú
    const menuLinks = menuPrincipal.querySelectorAll('a');
    console.log('🔗 Enlaces encontrados:', menuLinks.length);
    
    menuLinks.forEach((link, index) => {
        link.addEventListener('click', function(e) {
            console.log(`🖱️ Clic en enlace ${index + 1}:`, link.textContent.trim());
            
            // Si es un enlace de prueba (que empiece con #), prevenir navegación
            if (link.getAttribute('href').startsWith('#')) {
                e.preventDefault();
            }
            
            closeMenu();
        });
        
        // Debug adicional para cada enlace
        link.addEventListener('mouseenter', function() {
            console.log('🖱️ Mouse sobre:', link.textContent.trim());
        });
    });

    // Event listener para ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMenuOpen) {
            console.log('⌨️ ESC presionado - cerrando menú');
            closeMenu();
        }
    });

    // Función de debounce para optimizar el resize
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Handler optimizado para resize
    const handleResize = debounce(() => {        console.log('📏 Resize detectado - ancho:', window.innerWidth);
        if (window.innerWidth > 768 && isMenuOpen) {
            console.log('🖥️ Cambiando a desktop - cerrando menú');
            closeMenu();
        }
        // Asegurar que el overflow se restaure en desktop
        if (window.innerWidth > 768) {
            document.body.style.overflow = '';
        }
    }, 250);

    window.addEventListener('resize', handleResize);

    // Detectar cambios de viewport height (útil para teclados virtuales en móviles)
    let initialViewportHeight = window.innerHeight;
    window.addEventListener('resize', debounce(() => {
        const currentViewportHeight = window.innerHeight;
        const heightDifference = initialViewportHeight - currentViewportHeight;
        
        // Si la altura cambió significativamente (probablemente teclado virtual)
        if (Math.abs(heightDifference) > 150 && isMenuOpen) {
            // Ajustar el menú para teclados virtuales
            if (heightDifference > 150) {
                menuPrincipal.style.maxHeight = `${currentViewportHeight - 60}px`;
            } else {
                menuPrincipal.style.maxHeight = '';
            }
        }
    }, 100));

    // Event listener para clics fuera del menú
    document.addEventListener('click', function(e) {
        // Si el menú está abierto y el clic no fue en el menú ni en el botón toggle
        if (isMenuOpen && 
            !menuPrincipal.contains(e.target) && 
            !menuToggle.contains(e.target)) {
            closeMenu();
        }
    });

    // Prevenir que los clics dentro del menú cierren el menú
    menuPrincipal.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Detectar orientación en móviles
    window.addEventListener('orientationchange', function() {
        setTimeout(() => {
            if (isMenuOpen) {
                closeMenu();
            }
        }, 100);
    });

    console.log('✅ Navbar JavaScript inicializado correctamente - Panel Simple');
});
