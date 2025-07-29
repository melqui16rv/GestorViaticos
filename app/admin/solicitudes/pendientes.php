<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/metodos_solicitud.php';

requireRole(['7', '1']);

$miClase = new solicitudRol();
$usuario = null;
if (isset($_SESSION['numero_documento'])) {
    $usuario = $miClase->obtenerDatosUsuarioLogueado($_SESSION['numero_documento']);
    if ($usuario && is_array($usuario)) {
        $usuario = $usuario[0];
    }
}

// Verificar que el usuario sea administrador
if (!$miClase->esAdministrador($usuario['numero_documento'])) {
    header('Location: ' . BASE_URL . 'includes/session/unauthorized.php');
    exit;
}

// Obtener solicitudes pendientes y resumen
$solicitudesPendientes = $miClase->obtenerSolicitudesEnviadas();
$resumenSolicitudes = $miClase->obtenerResumenSolicitudes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Solicitudes de Rol - Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/dashboard_content.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        .solicitud-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #3b82f6;
        }
        .solicitud-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        .btn-accept {
            background: #10b981;
            color: white;
        }
        .btn-accept:hover {
            background: #059669;
        }
        .btn-reject {
            background: #ef4444;
            color: white;
        }
        .btn-reject:hover {
            background: #dc2626;
        }
        .btn-view {
            background: #6366f1;
            color: white;
        }
        .btn-view:hover {
            background: #4f46e5;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
        }
        .stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
    <div class="dashboard-container dashboard-background bg-white rounded-lg p-6 shadow-lg">
        
        <main class="main-content">
            <!-- Estadísticas Resumen -->
            <div class="stats-grid">
                <?php 
                $totales = ['enviada' => 0, 'aceptada' => 0, 'rechazada' => 0];
                foreach ($resumenSolicitudes as $resumen) {
                    $totales[$resumen['estado']] = $resumen['total'];
                }
                ?>
                <div class="stat-card">
                    <div class="stat-number text-yellow-600"><?php echo $totales['enviada']; ?></div>
                    <div class="stat-label">Pendientes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number text-green-600"><?php echo $totales['aceptada']; ?></div>
                    <div class="stat-label">Aceptadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number text-red-600"><?php echo $totales['rechazada']; ?></div>
                    <div class="stat-label">Rechazadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number text-blue-600"><?php echo array_sum($totales); ?></div>
                    <div class="stat-label">Total</div>
                </div>
            </div>

            <!-- Navegación de pestañas -->
            <div class="mb-6">
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                    <button class="tab-btn active" data-tab="pendientes">
                        <i class="fas fa-clock mr-2"></i>Solicitudes Pendientes (<?php echo $totales['enviada']; ?>)
                    </button>
                    <button class="tab-btn" data-tab="historial">
                        <i class="fas fa-history mr-2"></i>Historial Completo
                    </button>
                </div>
            </div>

            <!-- Contenido de Solicitudes Pendientes -->
            <div id="tab-pendientes" class="tab-content active">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Solicitudes Pendientes de Aprobación</h3>
                    
                    <?php if (empty($solicitudesPendientes)): ?>
                        <div class="text-center py-8">
                            <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">¡Todo al día!</h3>
                            <p class="text-gray-500">No hay solicitudes pendientes por revisar.</p>
                        </div>
                    <?php else: ?>
                        <div id="solicitudes-pendientes">
                            <?php foreach ($solicitudesPendientes as $solicitud): ?>
                                <div class="solicitud-card" data-solicitud="<?php echo $solicitud['id_solicitud']; ?>">
                                    <div class="solicitud-header">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-800 mb-1">
                                                <?php echo htmlspecialchars($solicitud['nombre_completo']); ?>
                                            </h4>
                                            <p class="text-sm text-gray-600">
                                                <i class="fas fa-id-card mr-1"></i>
                                                <?php echo htmlspecialchars($solicitud['numero_documento']); ?>
                                                •
                                                <i class="fas fa-envelope mr-1"></i>
                                                <?php echo htmlspecialchars($solicitud['email_usuario']); ?>
                                            </p>
                                        </div>
                                        <span class="badge badge-pending">Pendiente</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Rol Solicitado:</label>
                                            <p class="text-gray-900">
                                                <?php echo $solicitud['rol_nombre'] ? htmlspecialchars($solicitud['rol_nombre']) : htmlspecialchars($solicitud['id_rol_solicitado']); ?>
                                            </p>
                                        </div>
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Fecha de Solicitud:</label>
                                            <p class="text-gray-900">
                                                <?php echo date('d/m/Y H:i', strtotime($solicitud['fecha_solicitud'])); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="text-sm font-medium text-gray-700">Motivo/Justificación:</label>
                                        <p class="text-gray-900 bg-gray-50 p-3 rounded-lg mt-1">
                                            <?php echo nl2br(htmlspecialchars($solicitud['motivo'])); ?>
                                        </p>
                                    </div>
                                    
                                    <div class="flex flex-wrap gap-2">
                                        <button class="btn-action btn-view" onclick="verDetalleSolicitud(<?php echo $solicitud['id_solicitud']; ?>)">
                                            <i class="fas fa-eye mr-1"></i>Ver Detalle
                                        </button>
                                        <button class="btn-action btn-accept" onclick="mostrarModalAceptar(<?php echo $solicitud['id_solicitud']; ?>)">
                                            <i class="fas fa-check mr-1"></i>Aceptar
                                        </button>
                                        <button class="btn-action btn-reject" onclick="mostrarModalRechazar(<?php echo $solicitud['id_solicitud']; ?>)">
                                            <i class="fas fa-times mr-1"></i>Rechazar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Contenido de Historial -->
            <div id="tab-historial" class="tab-content" style="display: none;">
                <div class="bg-white rounded-lg p-6 shadow-lg">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historial de Solicitudes</h3>
                    
                    <!-- Filtros -->
                    <div class="mb-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado:</label>
                            <select id="filtro-estado" class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="">Todos</option>
                                <option value="enviada">Pendientes</option>
                                <option value="aceptada">Aceptadas</option>
                                <option value="rechazada">Rechazadas</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio:</label>
                            <input type="date" id="filtro-fecha-inicio" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin:</label>
                            <input type="date" id="filtro-fecha-fin" class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div class="flex items-end">
                            <button id="btn-filtrar" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                                <i class="fas fa-filter mr-1"></i>Filtrar
                            </button>
                        </div>
                    </div>
                    
                    <!-- Tabla de historial -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol Solicitado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Solicitud</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Respuesta</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-historial" class="bg-white divide-y divide-gray-200">
                                <!-- Se llena dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para Aceptar Solicitud -->
    <div id="modalAceptar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Aceptar Solicitud</h3>
                <p class="text-gray-600 mb-4">¿Está seguro que desea aceptar esta solicitud? El rol del usuario será actualizado inmediatamente.</p>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones (opcional):</label>
                    <textarea id="observaciones-aceptar" class="w-full p-3 border border-gray-300 rounded-lg" rows="3" placeholder="Ingrese observaciones adicionales..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="cerrarModalAceptar()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button onclick="confirmarAceptar()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Aceptar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Rechazar Solicitud -->
    <div id="modalRechazar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Rechazar Solicitud</h3>
                <p class="text-gray-600 mb-4">¿Está seguro que desea rechazar esta solicitud?</p>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo del rechazo <span class="text-red-500">*</span>:</label>
                    <textarea id="observaciones-rechazar" class="w-full p-3 border border-gray-300 rounded-lg" rows="3" placeholder="Explique el motivo del rechazo..." required></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button onclick="cerrarModalRechazar()" class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button onclick="confirmarRechazar()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Rechazar Solicitud
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-btn {
            flex: 1;
            padding: 0.75rem 1rem;
            text-align: center;
            border-radius: 0.5rem;
            transition: all 0.2s;
            cursor: pointer;
            background: transparent;
            border: none;
            color: #6b7280;
        }
        .tab-btn.active {
            background: white;
            color: #1f2937;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>

    <script>
        let solicitudIdActual = null;

        // Gestión de pestañas
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const tab = this.dataset.tab;
                
                // Actualizar botones
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Actualizar contenido
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.remove('active');
                    content.style.display = 'none';
                });
                
                const targetTab = document.getElementById(`tab-${tab}`);
                if (targetTab) {
                    targetTab.classList.add('active');
                    targetTab.style.display = 'block';
                    
                    // Si es el historial, cargar datos
                    if (tab === 'historial') {
                        cargarHistorial();
                    }
                }
            });
        });

        // Modales
        function mostrarModalAceptar(idSolicitud) {
            solicitudIdActual = idSolicitud;
            document.getElementById('modalAceptar').classList.remove('hidden');
            document.getElementById('observaciones-aceptar').value = '';
        }

        function cerrarModalAceptar() {
            document.getElementById('modalAceptar').classList.add('hidden');
            solicitudIdActual = null;
        }

        function mostrarModalRechazar(idSolicitud) {
            solicitudIdActual = idSolicitud;
            document.getElementById('modalRechazar').classList.remove('hidden');
            document.getElementById('observaciones-rechazar').value = '';
        }

        function cerrarModalRechazar() {
            document.getElementById('modalRechazar').classList.add('hidden');
            solicitudIdActual = null;
        }

        // Confirmar acciones
        function confirmarAceptar() {
            if (!solicitudIdActual) return;
            
            const observaciones = document.getElementById('observaciones-aceptar').value;
            
            fetch('control/ajax_solicitud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=aceptar_solicitud&id_solicitud=${solicitudIdActual}&observaciones=${encodeURIComponent(observaciones)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
            });
            
            cerrarModalAceptar();
        }

        function confirmarRechazar() {
            if (!solicitudIdActual) return;
            
            const observaciones = document.getElementById('observaciones-rechazar').value.trim();
            
            if (!observaciones) {
                Swal.fire('Error', 'Debe proporcionar un motivo para el rechazo', 'error');
                return;
            }
            
            fetch('control/ajax_solicitud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=rechazar_solicitud&id_solicitud=${solicitudIdActual}&observaciones=${encodeURIComponent(observaciones)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', data.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'No se pudo procesar la solicitud', 'error');
            });
            
            cerrarModalRechazar();
        }

        // Ver detalle de solicitud
        function verDetalleSolicitud(idSolicitud) {
            fetch('control/ajax_solicitud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=obtener_solicitud_detalle&id_solicitud=${idSolicitud}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const solicitud = data.data;
                    Swal.fire({
                        title: 'Detalle de Solicitud',
                        html: `
                            <div class="text-left">
                                <p><strong>Usuario:</strong> ${solicitud.nombre_completo}</p>
                                <p><strong>Documento:</strong> ${solicitud.numero_documento}</p>
                                <p><strong>Email:</strong> ${solicitud.email_usuario}</p>
                                <p><strong>Teléfono:</strong> ${solicitud.telefono || 'No disponible'}</p>
                                <p><strong>Rol Solicitado:</strong> ${solicitud.rol_nombre || solicitud.id_rol_solicitado}</p>
                                <p><strong>Fecha Solicitud:</strong> ${new Date(solicitud.fecha_solicitud).toLocaleString()}</p>
                                <p><strong>Estado:</strong> ${solicitud.estado}</p>
                                <p><strong>Motivo:</strong></p>
                                <div class="bg-gray-100 p-2 rounded mt-1">${solicitud.motivo.replace(/\n/g, '<br>')}</div>
                            </div>
                        `,
                        width: 600,
                        confirmButtonText: 'Cerrar'
                    });
                } else {
                    Swal.fire('Error', 'No se pudo obtener el detalle de la solicitud', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'No se pudo cargar el detalle', 'error');
            });
        }

        // Cargar historial con filtros
        function cargarHistorial() {
            const estado = document.getElementById('filtro-estado').value;
            const fechaInicio = document.getElementById('filtro-fecha-inicio').value;
            const fechaFin = document.getElementById('filtro-fecha-fin').value;
            
            fetch('control/ajax_solicitud.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=obtener_solicitudes&estado=${estado}&fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const tbody = document.getElementById('tabla-historial');
                    tbody.innerHTML = '';
                    
                    data.data.forEach(solicitud => {
                        const estadoBadge = getEstadoBadge(solicitud.estado);
                        const fechaRespuesta = solicitud.fecha_respuesta ? new Date(solicitud.fecha_respuesta).toLocaleDateString() : '-';
                        
                        tbody.innerHTML += `
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    <div>
                                        <div class="font-medium text-gray-900">${solicitud.nombre_completo}</div>
                                        <div class="text-gray-500">${solicitud.numero_documento}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    ${solicitud.rol_nombre || solicitud.id_rol_solicitado}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    ${new Date(solicitud.fecha_solicitud).toLocaleDateString()}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    ${estadoBadge}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">${fechaRespuesta}</td>
                                <td class="px-4 py-3 text-sm">
                                    <button onclick="verDetalleSolicitud(${solicitud.id_solicitud})" class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    Swal.fire('Error', 'No se pudo cargar el historial', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Error al cargar los datos', 'error');
            });
        }

        function getEstadoBadge(estado) {
            switch(estado) {
                case 'enviada':
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pendiente</span>';
                case 'aceptada':
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aceptada</span>';
                case 'rechazada':
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rechazada</span>';
                default:
                    return '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">' + estado + '</span>';
            }
        }

        // Event listener para filtros
        document.getElementById('btn-filtrar').addEventListener('click', cargarHistorial);

        // Cerrar modales con ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModalAceptar();
                cerrarModalRechazar();
            }
        });
    </script>
