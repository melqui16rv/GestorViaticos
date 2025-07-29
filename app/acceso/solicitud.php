<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/metodos_solicitud.php';

$miClase = new solicitudRol();
$usuario = null;
if (isset($_SESSION['numero_documento'])) {
    $usuario = $miClase->obtenerDatosUsuarioLogueado($_SESSION['numero_documento']);
    if ($usuario && is_array($usuario)) {
        $usuario = $usuario[0];
    }
}

// Obtener roles disponibles (excepto el 7)
$rolesDisponibles = $miClase->obtenerRolesDisponibles();
?>
<head>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<div class="dashboard-container" id="dashboardVisitasApre">
    <div class="rounded-lg">
        <div class="flex justify-end mb-4">
            <a href="javascript:void(0);" id="toggleFormButtonVisitasApre" class="actualizar-tabla-link inline-block">
                <button type="button" class="actualizar-tabla-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-refresh" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span id="toggleFormButtonTextVisitasApre">Agregar Solicitud</span>
                </button>
            </a>
        </div>
        <form id="formVisitasApreUnique" method="POST" class="formulario formulario-visitasapre" style="display: none;">
            <input type="hidden" name="action" value="create" id="formAction">
            <input type="hidden" name="id_solicitud" id="id_solicitud_edit">
            
            <!-- Sección de Perfil -->
            <div class="form-section">
                <h3 class="form-section-title">📋 Información de Perfil</h3>
                <p class="form-section-description">
                    <strong>⚠️ IMPORTANTE:</strong> Debe completar su información de perfil antes de solicitar un cambio de rol.
                    <br><span style="color: #dc3545;">Todos los campos son obligatorios.</span>
                </p>
                
                <div class="form-group">
                    <label>*Número de Documento:</label>
                    <input class="input-form" type="text" name="numero_documento" id="numero_documento" required
                           placeholder="Ej: 12345678 (sin puntos ni espacios)" 
                           value="<?php 
                           if ($usuario) {
                               // Si el teléfono es 'Sin teléfono', no mostrar el número de documento, solo el placeholder
                               if ($usuario['telefono'] === 'Sin teléfono') {
                                   echo '';
                               } else {
                                   // Si el teléfono es diferente de 'Sin teléfono', mostrar el número de documento
                                   echo htmlspecialchars($usuario['numero_documento']);
                               }
                           } else {
                               echo '';
                           }
                           ?>">
                    <small class="form-help">Solo números, sin puntos ni espacios</small>
                </div>
                
                <div class="form-group">
                    <label>*Tipo de Documento:</label>
                    <select class="input-form" name="tipo_doc" id="tipo_doc" required>
                        <option value="">Seleccione tipo de documento</option>
                        <option value="CC" <?php echo ($usuario && $usuario['tipo_doc'] == 'CC') ? 'selected' : ''; ?>>Cédula de Ciudadanía</option>
                        <option value="CE" <?php echo ($usuario && $usuario['tipo_doc'] == 'CE') ? 'selected' : ''; ?>>Cédula de Extranjería</option>
                        <option value="TI" <?php echo ($usuario && $usuario['tipo_doc'] == 'TI') ? 'selected' : ''; ?>>Tarjeta de Identidad</option>
                        <option value="RC" <?php echo ($usuario && $usuario['tipo_doc'] == 'RC') ? 'selected' : ''; ?>>Registro Civil</option>
                        <option value="PA" <?php echo ($usuario && $usuario['tipo_doc'] == 'PA') ? 'selected' : ''; ?>>Pasaporte</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>*Teléfono:</label>
                    <input class="input-form" type="tel" name="telefono" id="telefono" required 
                           placeholder="Ej: 3001234567 (solo números)" 
                           value="<?php 
                           if ($usuario) {
                               // Si el teléfono es 'Sin teléfono', no mostrar el valor, solo el placeholder
                               if ($usuario['telefono'] === 'Sin teléfono') {
                                   echo '';
                               } else {
                                   // Si el teléfono es diferente de 'Sin teléfono', mostrar el número real
                                   echo htmlspecialchars($usuario['telefono']);
                               }
                           } else {
                               echo '';
                           }
                           ?>">
                    <small class="form-help">Solo números, mínimo 10 dígitos, máximo 15</small>
                </div>
            </div>
            
            <!-- Sección de Solicitud -->
            <div class="form-section">
                <h3 class="form-section-title">🔄 Solicitud de Cambio de Rol</h3>
                <p class="form-section-description">Seleccione el rol que desea solicitar y proporcione la justificación.</p>
                
                <div class="form-group">
                    <label>*Rol de Solicitud:</label>
                    <select class="input-form" name="id_rol_solicitado" id="id_rol_solicitado" required onchange="mostrarOtroRol(this)">
                        <option value="">Seleccione un rol</option>
                        <?php foreach ($rolesDisponibles as $rol): ?>
                            <option value="<?php echo $rol['id_rol']; ?>"><?php echo htmlspecialchars($rol['nombre_rol']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <div class="form-group" id="otroRolGroup" style="display:none;">
                <label>Especifique el otro rol:</label>
                <input class="input-form" type="text" name="otro_rol" id="otro_rol" placeholder="Ejemplo: Gestor Presupuestal">
            </div>
            <div class="form-group">
                <label>¿Concideraciones para el Administrador?</label>
                <textarea name="motivo" id="motivo_edit" class="input-form" placeholder="Ejemplo: Necesito este rol para gestionar usuarios"></textarea>
            </div>
            <div class="form-buttons">
                <button type="submit" class="btn btn-primary" id="btnGuardar">Guardar</button>
                <button type="button" class="btn btn-secondary" id="btnCancelar">Cancelar</button>
            </div>
        </form>
        
    </div>
    <div class="container mx-auto px-4" style="margin-top:20px;">
        <div class="bg-white rounded-lg shadow-md mb-6">
            <div class="p-6 justify-center">
                <h2 class="text-xl font-semibold text-gray-700 mb-4 flex justify-center">Mis Solicitudes de Rol</h2>
                <div class="p-6"></div>
                <div class="tabla-card">
                    <table class="tabla">
                        <thead>
                            <tr>
                                <th>Rol Solicitado</th>
                                <th>Motivo</th>
                                <th>Fecha Solicitud</th>
                                <th>Estado</th>
                                <th>Fecha Respuesta</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if ($usuario) {
                                $misSolicitudes = $miClase->obtenerSolicitudesPorUsuario($usuario['numero_documento']);
                                foreach ($misSolicitudes as $solicitud): ?>
                            <tr>
                                <td><?php echo $solicitud['rol_nombre'] ? htmlspecialchars($solicitud['rol_nombre']) : htmlspecialchars($solicitud['id_rol_solicitado']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['motivo']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['fecha_solicitud']); ?></td>
                                <td><?php echo htmlspecialchars($solicitud['estado']); ?></td>
                                <td><?php echo $solicitud['fecha_respuesta'] ? htmlspecialchars($solicitud['fecha_respuesta']) : '-'; ?></td>
                                <td>
                                    <?php if ($solicitud['estado'] === 'enviada'): ?>
                                        <!-- <button type="button" class="btn btn-warning btn-editar-solicitud" 
                                                data-id="<?php //echo $solicitud['id_solicitud']; ?>" 
                                                data-rol="<?php //echo htmlspecialchars($solicitud['id_rol_solicitado']); ?>" 
                                                data-motivo="<?php //echo htmlspecialchars($solicitud['motivo']); ?>">
                                            <i class="fas fa-edit"></i> Editar -->
                                        </button>
                                        <button type="button" class="btn btn-danger" onclick="eliminarSolicitudAjax('<?php echo $solicitud['id_solicitud']; ?>')">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php 
                                endforeach;
                            } else {
                                echo '<tr><td colspan="6">Debe iniciar sesión para ver sus solicitudes</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/acceso/style_acceso.css">
<style>
/* Estilos para las secciones del formulario */
.form-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.form-section-title {
    color: #495057;
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-section-description {
    color: #6c757d;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
    line-height: 1.4;
}

.form-help {
    display: block;
    color: #6c757d;
    font-size: 0.8rem;
    margin-top: 0.25rem;
    font-style: italic;
}

/* Mejorar estilos de inputs */
.input-form {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    font-size: 1rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.input-form:focus {
    border-color: #80bdff;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #495057;
    font-weight: 500;
}

/* Validación visual para campos requeridos */
.input-form:invalid {
    border-color: #dc3545;
}

.input-form:valid {
    border-color: #28a745;
}

/* Forzar display para formulario - Importante para el toggle */
#formVisitasApreUnique {
    display: none !important;
}

#formVisitasApreUnique[style*="block"] {
    display: block !important;
}

/* Asegurar que el botón sea clickeable */
#toggleFormButtonVisitasApre {
    cursor: pointer !important;
    pointer-events: auto !important;
    z-index: 1000;
    position: relative;
}

/* Estilos para sección de solicitud deshabilitada */
.form-section:last-of-type[style*="opacity: 0.5"] {
    position: relative;
}

.form-section:last-of-type[style*="opacity: 0.5"]::before {
    content: "🔒 Complete su perfil para habilitar esta sección";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(255, 255, 255, 0.9);
    padding: 10px 20px;
    border-radius: 5px;
    font-weight: bold;
    color: #856404;
    border: 2px solid #ffeaa7;
    z-index: 10;
}

/* Mejorar visualización de campos bloqueados */
.input-form:disabled,
.input-form[readonly] {
    background-color: #f8f9fa !important;
    cursor: not-allowed !important;
    opacity: 0.7;
}

/* Mensaje de perfil destacado */
#mensaje-perfil {
    font-size: 14px !important;
    margin: 15px 0 !important;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Debug para verificar elementos */
.debug-highlight {
    border: 2px solid red !important;
    background-color: rgba(255, 0, 0, 0.1) !important;
}

/* Estilos responsivos */
@media (max-width: 768px) {
    .form-section {
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .form-section-title {
        font-size: 1.1rem;
    }
}
</style>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Variables globales
let btn, form, btnCancelar;

// Función para logging de debug
function debugLog(message) {
    console.log('[FORM DEBUG]', message);
}

// Función para verificar que los elementos estén disponibles
function verificarElementosDisponibles() {
    const btn = document.getElementById('toggleFormButtonVisitasApre');
    const form = document.getElementById('formVisitasApreUnique');
    
    if (!btn || !form) {
        debugLog('❌ CRITICAL: Elementos no encontrados después de cargar DOM');
        debugLog('Botón: ' + !!btn + ', Formulario: ' + !!form);
        return false;
    }
    
    debugLog('✅ Elementos verificados correctamente');
    return true;
}

// Inicializar elementos después de que el DOM esté listo
function initializeElements() {
    // Verificar primero que los elementos estén disponibles
    if (!verificarElementosDisponibles()) {
        debugLog('❌ ABORTANDO inicialización - elementos no disponibles');
        return false;
    }
    
    btn = document.getElementById('toggleFormButtonVisitasApre');
    form = document.getElementById('formVisitasApreUnique');
    btnCancelar = document.getElementById('btnCancelar');
    
    // Verificar que los elementos existen
    debugLog('Inicializando elementos...');
    debugLog('Elementos encontrados: btn=' + !!btn + ', form=' + !!form + ', btnCancelar=' + !!btnCancelar);
    
    if (btn) {
        debugLog('Botón encontrado con ID: ' + btn.id);
        debugLog('Texto del botón: ' + btn.textContent.trim());
    } else {
        debugLog('ERROR: Botón no encontrado');
    }
    
    if (form) {
        debugLog('Formulario encontrado con ID: ' + form.id);
        debugLog('Display inicial del formulario: "' + form.style.display + '"');
        debugLog('Computed display: ' + window.getComputedStyle(form).display);
    } else {
        debugLog('ERROR: Formulario no encontrado');
    }
    
    // Mostrar/ocultar formulario
    if(btn && form){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            debugLog('🔥 ¡BOTÓN CLICKEADO!');
            debugLog('Estado actual del form.style.display: "' + form.style.display + '"');
            debugLog('Computed display antes del cambio: ' + window.getComputedStyle(form).display);
            
            try {
                if (form.style.display === 'none' || form.style.display === '') {
                    // Verificar primero si puede crear una nueva solicitud
                    validarAntesDeCrearSolicitud()
                        .then(puedeCrear => {
                            if (puedeCrear) {
                                // Mostrar formulario
                                form.style.display = 'block';
                                debugLog('✅ Formulario mostrado - nuevo display: "' + form.style.display + '"');
                                
                                // Verificar estado después de mostrar
                                setTimeout(() => {
                                    verificarSolicitudPendiente();
                                }, 100);
                                
                                // Hacer scroll al formulario para asegurar visibilidad
                                setTimeout(() => {
                                    form.scrollIntoView({behavior: 'smooth', block: 'center'});
                                }, 200);
                            }
                            // Si no puede crear, ya se mostró el mensaje en validarAntesDeCrearSolicitud()
                        })
                        .catch(error => {
                            debugLog('❌ ERROR al validar: ' + error.message);
                            // En caso de error, permitir mostrar el formulario
                            form.style.display = 'block';
                            verificarSolicitudPendiente();
                        });
                } else {
                    form.style.display = 'none';
                    debugLog('❌ Formulario oculto - nuevo display: "' + form.style.display + '"');
                }
                
                debugLog('Computed display después del cambio: ' + window.getComputedStyle(form).display);
                limpiarFormulario();
            } catch (error) {
                debugLog('❌ ERROR en toggle: ' + error.message);
                // Fallback: simplemente toggle el formulario
                if (form.style.display === 'none' || form.style.display === '') {
                    form.style.display = 'block';
                } else {
                    form.style.display = 'none';
                }
            }
        });
        debugLog('✅ Event listener agregado al botón');
    } else {
        debugLog('❌ ERROR: No se pudo agregar event listener - btn=' + !!btn + ', form=' + !!form);
    }
    
    if(btnCancelar && form){
        btnCancelar.addEventListener('click', function(){
            debugLog('Botón cancelar clickeado');
            form.style.display = 'none';
            limpiarFormulario();
        });
        debugLog('Event listener agregado al botón cancelar');
    }
    
    debugLog('✅ initializeElements() completado exitosamente');
    return true;
}
function limpiarFormulario() {
    debugLog('Limpiando formulario...');
    document.getElementById('formAction').value = 'create';
    document.getElementById('id_solicitud_edit').value = '';
    document.getElementById('id_rol_solicitado').value = '';
    document.getElementById('motivo_edit').value = '';
    document.getElementById('otroRolGroup').style.display = 'none';
    document.getElementById('otro_rol').value = '';
    
    // NO limpiar campos de perfil - mantener los datos que el usuario esté ingresando
    // Solo resetear validaciones visuales
    const camposPerfil = ['numero_documento', 'tipo_doc', 'telefono'];
    camposPerfil.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.style.borderColor = '#ced4da';
            field.setCustomValidity('');
        }
    });
    
    // Re-evaluar control de sección de solicitud
    setTimeout(() => {
        controlarSeccionSolicitud();
    }, 100);
}

// Función para validar teléfono
function validarTelefono(input) {
    const telefono = input.value.replace(/\D/g, ''); // Solo números
    const esValido = telefono.length >= 10 && telefono.length <= 15;
    
    // Solo permitir números
    input.value = telefono;
    
    if (input.value !== '' && !esValido) {
        input.style.borderColor = '#dc3545';
        input.setCustomValidity('El teléfono debe tener entre 10 y 15 dígitos');
        return false;
    } else if (input.value !== '') {
        input.style.borderColor = '#28a745';
        input.setCustomValidity('');
        return true;
    } else {
        input.style.borderColor = '#ced4da';
        input.setCustomValidity('');
        return false;
    }
}

// Función para validar número de documento
function validarNumeroDocumento(input) {
    const documento = input.value.replace(/\D/g, ''); // Solo números
    const esValido = documento.length >= 6 && documento.length <= 15;
    
    // Solo permitir números
    input.value = documento;
    
    if (input.value !== '' && !esValido) {
        input.style.borderColor = '#dc3545';
        input.setCustomValidity('El número de documento debe tener entre 6 y 15 dígitos');
        return false;
    } else if (input.value !== '') {
        input.style.borderColor = '#28a745';
        input.setCustomValidity('');
        return true;
    } else {
        input.style.borderColor = '#ced4da';
        input.setCustomValidity('');
        return false;
    }
}

// Función para validar si el perfil está completo
function validarPerfilCompleto() {
    const numeroDoc = document.getElementById('numero_documento').value.trim();
    const tipoDoc = document.getElementById('tipo_doc').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    
    if (!numeroDoc || !tipoDoc || !telefono) {
        return false;
    }
    
    // Validar que el teléfono y documento sean válidos
    const telefonoValido = validarTelefono(document.getElementById('telefono'));
    const documentoValido = validarNumeroDocumento(document.getElementById('numero_documento'));
    
    return telefonoValido && documentoValido;
}

// Función para habilitar/deshabilitar sección de solicitud
function controlarSeccionSolicitud() {
    const isEdit = document.getElementById('formAction').value === 'edit';
    const perfilCompleto = validarPerfilCompleto();
    const seccionSolicitud = document.querySelector('.form-section:last-of-type');
    const camposSolicitud = seccionSolicitud.querySelectorAll('select, textarea, input');
    
    // Si estamos en modo edición, siempre habilitar la sección de solicitud
    if (isEdit) {
        seccionSolicitud.style.opacity = '1';
        seccionSolicitud.style.pointerEvents = 'auto';
        camposSolicitud.forEach(campo => {
            campo.disabled = false;
        });
        mostrarMensajePerfil('✏️ Editando solicitud. Los campos de perfil son opcionales.', 'info');
        return;
    }
    
    // Para creación nueva, validar perfil completo
    if (perfilCompleto) {
        seccionSolicitud.style.opacity = '1';
        seccionSolicitud.style.pointerEvents = 'auto';
        camposSolicitud.forEach(campo => {
            campo.disabled = false;
        });
        // Mostrar mensaje de éxito
        mostrarMensajePerfil('✅ Perfil completo. Ahora puede solicitar cambio de rol.', 'success');
    } else {
        seccionSolicitud.style.opacity = '0.5';
        seccionSolicitud.style.pointerEvents = 'none';
        camposSolicitud.forEach(campo => {
            campo.disabled = true;
        });
        // Mostrar mensaje de advertencia
        mostrarMensajePerfil('⚠️ Complete su perfil para poder solicitar cambio de rol.', 'warning');
    }
}

// Función para mostrar mensajes sobre el estado del perfil
function mostrarMensajePerfil(mensaje, tipo) {
    let messageDiv = document.getElementById('mensaje-perfil');
    if (!messageDiv) {
        messageDiv = document.createElement('div');
        messageDiv.id = 'mensaje-perfil';
        messageDiv.style.cssText = `
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-weight: 500;
            text-align: center;
            transition: all 0.3s ease;
        `;
        document.querySelector('.form-section').appendChild(messageDiv);
    }
    
    messageDiv.textContent = mensaje;
    
    if (tipo === 'success') {
        messageDiv.style.backgroundColor = '#d4edda';
        messageDiv.style.color = '#155724';
        messageDiv.style.border = '1px solid #c3e6cb';
    } else if (tipo === 'warning') {
        messageDiv.style.backgroundColor = '#fff3cd';
        messageDiv.style.color = '#856404';
        messageDiv.style.border = '1px solid #ffeaa7';
    } else if (tipo === 'info') {
        messageDiv.style.backgroundColor = '#d1ecf1';
        messageDiv.style.color = '#0c5460';
        messageDiv.style.border = '1px solid #bee5eb';
    }
}

// Función para validar antes de crear una nueva solicitud
function validarAntesDeCrearSolicitud() {
    return new Promise((resolve, reject) => {
        debugLog('🔍 Validando si puede crear nueva solicitud...');
        
        fetch('control/ajax_solicitud.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=validarAntesDeCrear'
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            debugLog('Respuesta validación: ' + JSON.stringify(data));
            
            if (data.success && !data.tiene_pendiente) {
                debugLog('✅ Puede crear nueva solicitud');
                resolve(true);
            } else {
                debugLog('❌ No puede crear nueva solicitud - ya tiene una pendiente');
                // Mostrar mensaje de error al usuario
                Swal.fire({
                    icon: 'warning',
                    title: 'Solicitud pendiente',
                    html: data.message,
                    confirmButtonText: 'Entendido',
                    showCancelButton: true,
                    cancelButtonText: 'Ver mis solicitudes',
                    reverseButtons: true
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
                        // Scroll hacia la tabla de solicitudes
                        const tablaSolicitudes = document.querySelector('.table-responsive');
                        if (tablaSolicitudes) {
                            tablaSolicitudes.scrollIntoView({behavior: 'smooth', block: 'center'});
                        }
                    }
                });
                resolve(false);
            }
        })
        .catch(error => {
            debugLog('❌ Error al validar: ' + error.message);
            console.error('Error al validar antes de crear:', error);
            // En caso de error, permitir la creación (fail-safe)
            resolve(true);
        });
    });
}

// Función para verificar si hay solicitud pendiente
function verificarSolicitudPendiente() {
    fetch('control/ajax_solicitud.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=verificarSolicitudPendiente'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        debugLog('Verificación de solicitud pendiente: ' + JSON.stringify(data));
        
        const camposPerfilInputs = ['numero_documento', 'tipo_doc', 'telefono'];
        
        if (data.tiene_pendiente) {
            debugLog('Usuario tiene solicitud pendiente - bloqueando campos de perfil');
            // Deshabilitar edición de perfil si hay solicitud pendiente
            camposPerfilInputs.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.readOnly = true;
                    field.disabled = true;
                    field.style.backgroundColor = '#f8f9fa';
                    field.style.cursor = 'not-allowed';
                    field.title = 'No puede editar este campo mientras tenga solicitudes pendientes';
                }
            });
            
            // Mostrar mensaje informativo
            mostrarMensajePerfil('ℹ️ Tiene una solicitud pendiente. Solo puede editar el rol solicitado.', 'info');
            
            // Habilitar sección de solicitud para edición
            const seccionSolicitud = document.querySelector('.form-section:last-of-type');
            seccionSolicitud.style.opacity = '1';
            seccionSolicitud.style.pointerEvents = 'auto';
            
        } else {
            debugLog('Usuario NO tiene solicitud pendiente - permitiendo edición completa');
            // Permitir edición de perfil
            camposPerfilInputs.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.readOnly = false;
                    field.disabled = false;
                    field.style.backgroundColor = '#ffffff';
                    field.style.cursor = 'text';
                    field.title = '';
                }
            });
            
            // Validar perfil para controlar acceso a solicitud
            controlarSeccionSolicitud();
        }
    })
    .catch(error => {
        debugLog('Error al verificar solicitud pendiente: ' + error);
        console.error('Error al verificar solicitud pendiente:', error);
        
        // En caso de error, permitir el uso normal del formulario
        debugLog('⚠️ Permitiendo uso del formulario por error en verificación');
        controlarSeccionSolicitud();
    });
}
function mostrarOtroRol(select) {
    var otroGroup = document.getElementById('otroRolGroup');
    var otroRolInput = document.getElementById('otro_rol');
    if(select.value === 'otro') {
        otroGroup.style.display = 'block';
        otroRolInput.required = true;
    } else {
        otroGroup.style.display = 'none';
        otroRolInput.required = false;
    }
}

// Función para cargar datos en el formulario para editar
let motivoAnterior = '';
function editarSolicitud(id, rol, motivo) {
    console.log('Editar:', {id, rol, motivo}); // DEPURACIÓN
    form.style.display = 'block';
    // Forzar scroll al formulario por si está fuera de vista
    setTimeout(() => {
        form.scrollIntoView({behavior: 'smooth', block: 'center'});
    }, 100);
    document.getElementById('formAction').value = 'edit';
    document.getElementById('id_solicitud_edit').value = id;
    // Seleccionar el valor del select correctamente (asegura coincidencia exacta)
    const selectRol = document.getElementById('id_rol_solicitado');
    for (let i = 0; i < selectRol.options.length; i++) {
        if (selectRol.options[i].value.trim().toLowerCase() === rol.trim().toLowerCase()) {
            selectRol.selectedIndex = i;
            break;
        }
    }
    // Disparar el evento change para mostrar el campo de otro rol si aplica
    selectRol.dispatchEvent(new Event('change'));
    // También llamar directamente a la función para asegurar que funcione
    mostrarOtroRol(selectRol);
    // Limpiar los campos de motivo y otro_rol
    document.getElementById('motivo_edit').value = '';
    document.getElementById('otro_rol').value = '';
    motivoAnterior = motivo || '';
    if(selectRol.value === 'otro') {
        document.getElementById('otroRolGroup').style.display = 'block';
        // Extraer valores previos si existen
        if (motivo) {
            // Soporta salto de línea y espacios
            const otroMatch = motivo.match(/Otro rol especificado:\s*([^;\n]*)/i);
            const consMatch = motivo.match(/Concideraciones:\s*([\s\S]*)/i);
            if (otroMatch) {
                document.getElementById('otro_rol').value = otroMatch[1].trim();
            }
            if (consMatch) {
                document.getElementById('motivo_edit').value = consMatch[1].replace(/^\s*;/, '').trim();
            }
        }
    } else {
        document.getElementById('otroRolGroup').style.display = 'none';
        if (motivo) {
            const consMatch = motivo.match(/Concideraciones:\s*([\s\S]*)/i);
            if (consMatch) {
                document.getElementById('motivo_edit').value = consMatch[1].replace(/^\s*;/, '').trim();
            }
        }
    }
    
    // Controlar la sección de solicitud para modo edición
    setTimeout(() => {
        controlarSeccionSolicitud();
    }, 100);
    
    Swal.fire({
        icon: 'info',
        title: 'Edición de solicitud',
        html: 'Si no haces cambios, se mantendrá la información como estaba antes.',
        confirmButtonText: 'Entendido'
    });
}

// Eliminar solicitud por AJAX
function eliminarSolicitudAjax(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('control/ajax_solicitud.php', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete&id_solicitud=' + encodeURIComponent(id)
            })
            .then(async response => {
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    const text = await response.text();
                    Swal.fire('Error', 'Respuesta inesperada del servidor: ' + text, 'error');
                    return;
                }
                if (data.success) {
                    Swal.fire('Eliminada', data.message, 'success');
                    // Eliminar la fila de la tabla sin recargar
                    const btn = document.querySelector('button[onclick="eliminarSolicitudAjax(\'' + id + '\')"]');
                    if (btn) {
                        const row = btn.closest('tr');
                        if (row) row.remove();
                    }
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'No se pudo eliminar la solicitud.', 'error');
            });
        }
    });
}

// Event listener para botones de editar
document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-editar-solicitud')) {
            const btn = e.target.closest('.btn-editar-solicitud');
            const id = btn.getAttribute('data-id');
            const rol = btn.getAttribute('data-rol');
            const motivo = btn.getAttribute('data-motivo');
            editarSolicitud(id, rol, motivo);
        }
    });
});    // Interceptar el submit del formulario para AJAX
const formSolicitud = document.getElementById('formVisitasApreUnique');
if(formSolicitud){
    formSolicitud.addEventListener('submit', function(e){
        e.preventDefault();
        debugLog('📤 Formulario enviado - iniciando validaciones...');
        
        // Verificar si estamos en modo edición o creación
        let isEdit = document.getElementById('formAction').value === 'edit';
        debugLog('🔍 Modo detectado: ' + (isEdit ? 'EDICIÓN' : 'CREACIÓN'));
        
        // Solo validar perfil completo si estamos CREANDO una nueva solicitud
        if (!isEdit) {
            const perfilCompleto = validarPerfilCompleto();
            debugLog('📋 Validación perfil en modo creación: ' + (perfilCompleto ? 'COMPLETO' : 'INCOMPLETO'));
            if (!perfilCompleto) {
                Swal.fire({
                    icon: 'error',
                    title: 'Perfil incompleto',
                    text: 'Debe completar toda su información de perfil antes de crear una solicitud.',
                    confirmButtonText: 'Entendido'
                });
                debugLog('❌ Envío bloqueado: Perfil incompleto (modo creación)');
                return;
            }
        } else {
            debugLog('✅ Modo edición: Saltando validación de perfil obligatorio');
        }
        
        // Validar que se haya seleccionado un rol
        const rolSeleccionado = document.getElementById('id_rol_solicitado').value;
        if (!rolSeleccionado) {
            Swal.fire({
                icon: 'error',
                title: 'Rol requerido',
                text: 'Debe seleccionar el rol que desea solicitar.',
                confirmButtonText: 'Entendido'
            });
            debugLog('❌ Envío bloqueado: No se seleccionó rol');
            return;
        }
        
        debugLog('✅ Validaciones pasadas - procesando envío...');
        
        const formData = new FormData(formSolicitud);
        const otroRol = formData.get('otro_rol') ? formData.get('otro_rol').trim() : '';
        const motivo = formData.get('motivo') ? formData.get('motivo').trim() : '';
        let motivoFinal = '';
        isEdit = document.getElementById('formAction').value === 'edit';
        
        if(isEdit) {
            debugLog('📝 Modo edición activado');
            // Si ambos campos están vacíos, enviar el motivo anterior
            if(otroRol === '' && motivo === '') {
                formData.set('motivo', motivoAnterior);
            } else {
                if(rolSeleccionado === 'otro' && otroRol !== '') {
                    motivoFinal += 'Otro rol especificado: ' + otroRol;
                }
                if(motivo !== '') {
                    if(motivoFinal !== '') motivoFinal += '; \n';
                    motivoFinal += 'Concideraciones: ' + motivo;
                }
                formData.set('motivo', motivoFinal);
            }
        } else {
            debugLog('🆕 Modo creación activado');
            if(rolSeleccionado === 'otro' && otroRol !== '') {
                motivoFinal += 'Otro rol especificado: ' + otroRol;
            }
            if(motivo !== '') {
                if(motivoFinal !== '') motivoFinal += '; \n';
                motivoFinal += 'Concideraciones: ' + motivo;
            }
            formData.set('motivo', motivoFinal);
        }
        
        // Cambiar acción según si es edición o creación
        let action = document.getElementById('formAction').value;
        formData.set('action', action);
        
        debugLog('📤 Enviando datos al servidor...');
        fetch('control/ajax_solicitud.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: new URLSearchParams([...formData])
        })
        .then(async response => {
            let data;
            try {
                data = await response.json();
            } catch (e) {
                const text = await response.text();
                Swal.fire('Error', 'Respuesta inesperada del servidor: ' + text, 'error');
                return;
            }
            if(data.success){
                Swal.fire('Éxito', data.message, 'success').then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Ocurrió un error.', 'error');
            }
        })
        .catch(() => {
            Swal.fire('Error', 'No se pudo procesar la solicitud.', 'error');
        });
    });
}

// Estilo dinámico para el input de otro_rol (placeholder gris, texto normal al escribir)
const otroRolInput = document.getElementById('otro_rol');
if (otroRolInput) {
    otroRolInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.add('input-form-filled');
        } else {
            this.classList.remove('input-form-filled');
        }
    });
    // Forzar el estilo correcto al cargar (por si hay valor prellenado)
    otroRolInput.dispatchEvent(new Event('input'));
}
// Estilo dinámico para el textarea de motivo (placeholder gris, texto normal al escribir)
const motivoInput = document.getElementById('motivo_edit');
if (motivoInput) {
    motivoInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
            this.classList.add('input-form-filled');
        } else {
            this.classList.remove('input-form-filled');
        }
    });
    motivoInput.dispatchEvent(new Event('input'));
}

// Inicialización del formulario
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar elementos del DOM
    debugLog('🚀 DOM completamente cargado, inicializando...');
    
    // Primero inicializar elementos
    const inicializacionExitosa = initializeElements();
    
    if (!inicializacionExitosa) {
        debugLog('❌ FALLO EN INICIALIZACIÓN - Reintentando en 1 segundo...');
        setTimeout(() => {
            debugLog('🔄 Reintentando inicialización...');
            initializeElements();
        }, 1000);
    }
    
    // Luego verificar estado de solicitudes (con un pequeño delay)
    setTimeout(() => {
        verificarSolicitudPendiente();
    }, 500);
    
    // Agregar validación en tiempo real a los campos de perfil
    const numeroDocInput = document.getElementById('numero_documento');
    const tipoDocInput = document.getElementById('tipo_doc');
    const telefonoInput = document.getElementById('telefono');
    
    if (numeroDocInput) {
        numeroDocInput.addEventListener('input', function() {
            validarNumeroDocumento(this);
            controlarSeccionSolicitud();
        });
        debugLog('✅ Validación de número de documento configurada');
    }
    
    if (tipoDocInput) {
        tipoDocInput.addEventListener('change', function() {
            controlarSeccionSolicitud();
        });
        debugLog('✅ Validación de tipo de documento configurada');
    }
    
    if (telefonoInput) {
        telefonoInput.addEventListener('input', function() {
            validarTelefono(this);
            controlarSeccionSolicitud();
        });
        debugLog('✅ Validación de teléfono configurada');
    }
    
    // Control inicial de la sección de solicitud
    setTimeout(() => {
        controlarSeccionSolicitud();
    }, 500);
    
    // Debug adicional para verificar el estado de los elementos
    setTimeout(function() {
        debugLog('=== VERIFICACIÓN POST-CARGA ===');
        const btnCheck = document.getElementById('toggleFormButtonVisitasApre');
        const formCheck = document.getElementById('formVisitasApreUnique');
        debugLog('Botón existe: ' + !!btnCheck);
        debugLog('Formulario existe: ' + !!formCheck);
        if (formCheck) {
            debugLog('Formulario display: "' + formCheck.style.display + '"');
            debugLog('Formulario computed display: ' + window.getComputedStyle(formCheck).display);
        }
    }, 1000);
});
</script>
