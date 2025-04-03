
document.addEventListener('keydown', function(event) {
    if ((event.ctrlKey && (event.key === '+' || event.key === '-' || event.key === '0')) || event.key === 'Escape') {
        event.preventDefault();
        document.body.style.zoom = '100%';
    }
});

window.addEventListener('resize', function() {
    document.body.style.zoom = '100%';
});
document.addEventListener('wheel', function(event) {
    if (event.ctrlKey) {
        event.preventDefault();
    }
}, { passive: false });

function redirigirSegunRol() {
    const rol = '<?php echo $rol; ?>';
    let url = '';
    
    switch(rol) {
        case '1':
            url = '<?php echo BASE_URL; ?>app/admin/index.php';
            break;
        case '2':
            url = '<?php echo BASE_URL; ?>app/gestor/index.php';
            break;
        case '3':
            url = '<?php echo BASE_URL; ?>app/presupuesto/index.php';
            break;
        default:
            url = '<?php echo BASE_URL; ?>index.php';
    }
    
    window.location.href = url;
}
