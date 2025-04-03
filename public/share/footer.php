<style>
    /* ---- FOOTER PROFESIONAL ---- */
footer {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 90%);
    color: var(--light-color);
    padding: 40px 0 30px;
    position: relative; /* Asegura que el footer esté en el flujo del documento */
    overflow: hidden;
    box-shadow: 0 -10px 25px rgba(0, 0, 0, 0.08);
    margin-top: 50px;
}

/* Efecto de ondas en el fondo */
footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, 
        var(--accent-color) 0%, 
        var(--secondary-color) 50%, 
        var(--accent-color) 100%);
    z-index: 1;
}

footer::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 60px;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1200 120' preserveAspectRatio='none'%3E%3Cpath d='M0,0V46.29c47.79,22.2,103.59,32.17,158,28,70.36-5.37,136.33-33.31,206.8-37.5C438.64,32.43,512.34,53.67,583,72.05c69.27,18,138.3,24.88,209.4,13.08,36.15-6,69.85-17.84,104.45-29.34C989.49,25,1113-14.29,1200,52.47V0Z' opacity='.1' fill='%23FFFFFF'/%3E%3C/svg%3E");
    background-size: cover;
    background-position: center;
    opacity: 0.15;
    pointer-events: none;
}

/* Contenedor central */
footer .container {
    width: 90%;
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
}

/* Botones de redes sociales */
.social-buttons {
    display: flex;
    gap: 15px;
    margin-bottom: 35px;
    justify-content: center;
    flex-wrap: wrap;
}

.social-link {
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: var(--light-color);
    padding: 12px 20px;
    border-radius: 30px;
    font-weight: 500;
    font-size: 15px;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(5px);
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.social-link i {
    font-size: 18px;
    margin-right: 10px;
    transition: transform var(--transition-normal);
}

.social-link span {
    position: relative;
    z-index: 2;
    letter-spacing: 0.5px;
}

/* Efectos específicos para cada red social */
.social-link:nth-child(1) {
    background: linear-gradient(45deg, rgba(59, 89, 152, 0.7), rgba(59, 89, 152, 0.4));
}

.social-link:nth-child(2) {
    background: linear-gradient(45deg, rgba(29, 161, 242, 0.7), rgba(29, 161, 242, 0.4));
}

.social-link:nth-child(3) {
    background: linear-gradient(45deg, rgba(0, 119, 181, 0.7), rgba(0, 119, 181, 0.4));
}

/* Efecto hover para los botones sociales */
.social-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
    transform: translateX(-100%) rotate(30deg);
    transition: transform 0.5s ease;
}

.social-link:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.social-link:hover::before {
    transform: translateX(100%) rotate(30deg);
}

.social-link:hover i {
    transform: scale(1.2);
}

/* Efectos para cada red social en hover */
.social-link:nth-child(1):hover {
    background: linear-gradient(45deg, rgba(59, 89, 152, 0.8), rgba(59, 89, 152, 0.6));
    box-shadow: 0 5px 15px rgba(59, 89, 152, 0.4);
}

.social-link:nth-child(2):hover {
    background: linear-gradient(45deg, rgba(29, 161, 242, 0.8), rgba(29, 161, 242, 0.6));
    box-shadow: 0 5px 15px rgba(29, 161, 242, 0.4);
}

.social-link:nth-child(3):hover {
    background: linear-gradient(45deg, rgba(0, 119, 181, 0.8), rgba(0, 119, 181, 0.6));
    box-shadow: 0 5px 15px rgba(0, 119, 181, 0.4);
}

/* Título del footer */
.tituloFooter {
    font-size: 14px;
    color: var(--gray-light);
    text-align: center;
    margin-top: 20px;
    position: relative;
    padding-top: 20px;
    font-weight: 500;
    letter-spacing: 1px;
}

.tituloFooter::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 1px;
    background: linear-gradient(90deg, 
        transparent 0%, 
        var(--gray-light) 50%, 
        transparent 100%);
}

/* Logo SENA estilizado */
.footer-logo {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 30px;
}

.footer-logo img {
    height: 50px;
    filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.2));
    transition: all var(--transition-normal);
}

.footer-logo:hover img {
    transform: scale(1.05);
    filter: drop-shadow(0 3px 8px rgba(0, 0, 0, 0.3)) brightness(1.1);
}

/* Enlaces adicionales del footer */
.footer-links {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    margin: 25px 0;
}

.footer-link {
    color: var(--gray-lighter);
    text-decoration: none;
    font-size: 14px;
    transition: all var(--transition-fast);
    position: relative;
}

.footer-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 1px;
    background-color: var(--accent-light);
    transition: width var(--transition-normal);
}

.footer-link:hover {
    color: var(--light-color);
}

.footer-link:hover::after {
    width: 100%;
}

/* Adaptaciones responsivas */
@media screen and (max-width: 768px) {
    footer {
        padding: 30px 0 20px;
    }
    
    .social-buttons {
        gap: 10px;
        margin-bottom: 25px;
    }
    
    .social-link {
        padding: 10px 15px;
        font-size: 14px;
    }
    
    .social-link i {
        font-size: 16px;
        margin-right: 8px;
    }
    
    .footer-links {
        gap: 15px;
        margin: 20px 0;
    }
    
    .tituloFooter {
        font-size: 12px;
    }
}

/* Animación para destacar la información de copyright */
@keyframes pulse-subtle {
    0%, 100% { opacity: 0.8; }
    50% { opacity: 1; }
}

.copyright-highlight {
    display: inline-block;
    font-weight: 600;
    color: var(--accent-light);
    animation: pulse-subtle 3s infinite;
}
</style>
<div class="contenedorFooter">

    <footer>
        <div class="container">
            <!-- Logo SENA (nuevo elemento) -->
            <div class="footer-logo">
                <img src="<?php echo BASE_URL; ?>assets/img/public/logo-sena-blanco.png" alt="Logo SENA">
            </div>
            
            <!-- Redes sociales mejoradas -->
            <div class="social-buttons">
                <a href="#" target="_blank" class="social-link">
                    <i class="fa-brands fa-facebook"></i>
                    <span>Facebook</span>
                </a>
                <a href="#" target="_blank" class="social-link">
                    <i class="fa-brands fa-twitter"></i>
                    <span>Twitter</span>
                </a>
                <a href="#" target="_blank" class="social-link">
                    <i class="fa-brands fa-linkedin"></i>
                    <span>LinkedIn</span>
                </a>
            </div>
            
            <!-- Enlaces adicionales (nuevo elemento) -->
            <div class="footer-links">
                <a href="#" class="footer-link">Términos y Condiciones</a>
                <a href="#" class="footer-link">Política de Privacidad</a>
                <a href="#" class="footer-link">Contacto</a>
                <a href="#" class="footer-link">Acerca de</a>
            </div>
            
            <!-- Copyright mejorado -->
            <p class="tituloFooter">© 2024 <span class="copyright-highlight">BANIN - SENA</span>. Todos los derechos reservados.</p>
        </div>
    </footer>
</div>