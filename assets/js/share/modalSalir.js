function salir() {
    const modal = document.getElementById('logoutModal');
    const confirmBtn = document.getElementById('confirmBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    
    modal.classList.add('active');
    
    return new Promise((resolve) => {
        confirmBtn.onclick = () => {
            modal.classList.remove('active');
            resolve(true);
            window.location.href = '<?php echo BASE_URL; ?>includes/session/salir.php';
        };
        
        cancelBtn.onclick = () => {
            modal.classList.remove('active');
            resolve(false);
        };
        
        modal.onclick = (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                resolve(false);
            }
        };
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const logoutButton = document.querySelector('a[href*="salir.php"]');
    if (logoutButton) {
        logoutButton.onclick = function(e) {
            e.preventDefault();
            salir();
        };
    }
});