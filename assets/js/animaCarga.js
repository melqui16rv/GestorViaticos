document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('loadingOverlay');
    const mainContent = document.getElementById('mainContent');

    setTimeout(() => {
        overlay.style.display = 'none';
        mainContent.classList.add('loaded');
    }, 1000);
});

document.addEventListener('DOMContentLoaded', function() {
    const preloadLink = document.querySelector('.preload-link');
    const loadingOverlay = document.getElementById('loadingOverlay');

    if (preloadLink) {
        preloadLink.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Mostrar el overlay de carga
            loadingOverlay.style.display = 'flex';

            // Crear una solicitud para precargar la página de destino
            fetch(preloadLink.href)
                .then(response => {
                    if (response.ok) {
                        // Cuando la página de destino se carga, redirigir al usuario
                        window.location.href = preloadLink.href;
                    } else {
                        console.error('Error al precargar la página');
                        loadingOverlay.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error de redireccionamiento:', error);
                    loadingOverlay.style.display = 'none';
                });
        });
    }
});