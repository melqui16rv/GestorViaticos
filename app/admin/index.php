<?php

ob_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosCDP.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/admin/metodosCRP.php';

requireRole(['1']);


$miClase = new user();
$miClase_Admin = new admin();


if (isset($_GET['busqueda'])) {
    $termino = $_GET['busqueda'];
    $datos = $miClase->buscar_usuario($termino);

    ob_clean();

    header('Content-Type: application/json');
    echo json_encode($datos);
    exit;
} else {
    $datos = $miClase->buscar_usuario('');
}
$datos = $miClase->buscar_usuario('');


ob_end_flush();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/links/usuariosAdmin.css">
    <link rel="stylesheet" href="../../assets/css/shareInFolder/styleTabla.css">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin/index_admin.css">
    <script src="<?php echo BASE_URL; ?>assets/js/admin/fun1.js"></script>
</head>
<body>
    <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php';
    ?>


    <div id="loadingOverlay" class="overlay">
        <div class="spinner-container">
            <div class="spinner"></div>
        </div>
    </div>

    <div id="mainContent" class="contenedor">
        <label class="toggle-switch">
            <input type="checkbox" id="viewToggle">
            <span class="slider">
                <span class="slider-text left">Usuarios</span>
                <span class="slider-text right">Candidatos</span>
            </span>
        </label>

        <div id="overlay-eliminar"></div>
        <div id="modal-eliminar">
            <div id="modal-header-eliminar">Confirmación</div>
            <div id="modal-message-eliminar">¿Estás seguro de que deseas eliminar este usuario?</div>
            <button class="modal-button-eliminar confirm-button-eliminar" onclick="confirmDelete()">Eliminar</button>
            <button class="modal-button-eliminar cancel-button-eliminar" onclick="closeModal()">Cancelar</button>
        </div>

        <div id="overlay-confirmacion"></div>
        <div id="modal-confirmacion">
            <div id="modal-header-confirmacion">Resultado</div>
            <div id="modal-message-confirmacion">El usuario ha sido eliminado exitosamente.</div>
            <button class="modal-button-confirmacion confirm-button-confirmacion" onclick="closeConfirmationModal()">Cerrar</button>
        </div>
        
        <div id="overlay-confirmacion"></div>
        <div id="modal-confirmacion">
            <div id="modal-header-confirmacion">Resultado</div>
            <div id="modal-message-confirmacion">Mensaje de confirmación aquí</div>
            <button class="modal-button-confirmacion confirm-button-confirmacion" onclick="closeConfirmationModal()">Cerrar</button>
        </div>
        
        <div id="usuariosView">
            <div class="buscador">
                <br>
                <h2 class="titulo" style="border-bottom: 2px solid;    border-color: #4a6fa5;    width: 300px;">Buscar</h2>
                <div class="formBusqueda">
                    <input type="text" id="buscar" name="buscar" class="codigo" 
                        placeholder="Buscar por documento o nombre">
                </div>
            </div>
            

            <br>
            <a href="<?php echo BASE_URL; ?>app/admin/control/formAgregarUsuario.php">
                <button class="perfil-btn" type="button">Agregar Usuario</button>
            </a>
            <h2>Resultados de la Consulta</h2>
            <div class="tablaGeneradaPorLaConsulta">
                <table>
                    <thead>
                        <tr>
                            <th class="border_left">Id rol</th>
                            <th>Número de documento</th>
                            <th>Tipo de documento</th>
                            <th>Nombre completo</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Rol</th>
                            <th style="text-align: center;">Editar</th>
                            <th class="border_right" style="text-align: center;">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody id="tablaUsuarios">
                        <?php foreach($datos as $row) { ?>
                        <tr>
                            <td><?php echo $row['id_rol']; ?></td>
                            <td><?php echo $row['numero_documento']; ?></td>
                            <td><?php echo $row['tipo_doc']; ?></td>
                            <td><?php echo $row['nombre_completo']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['telefono']; ?></td>
                            <td><?php echo $row['nombre_rol']; ?></td>
                            <td style="text-align: center;">
                                <a href="<?php echo BASE_URL; ?>app/admin/control/formEditarUsuario.php?numero=<?php echo $row['numero_documento']; ?>">
                                    <button class="editar-btn">&#9998;</button>
                                </a>
                            </td>
                            <td style="text-align: center;">
                                <button class="eliminar-btn" data-numero="<?php echo $row['numero_documento']; ?>">Eliminar</button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>
        </div>
        <div id="candidatosView">
            <!-- Formulario CDP -->
            <h1>Seleccionar archivo CDP</h1>
            <div class="conten-form_csv">
                <form action="control/procesarCDP.php" method="post" enctype="multipart/form-data" onsubmit="return validateCDPFile()">
                    <input type="file" name="dataCDP" id="file-input-cdp" style="display: none;" onchange="displayCDPFileName()">
                    <label for="file-input-cdp" class="file-input-label">
                        <i class="zmdi zmdi-upload zmdi-hc-2x"></i>
                        <span id="file-name-cdp">Elegir Archivo CSV</span>
                    </label>
                    <div class="botones">
                        <input type="submit" class="boton actualizar" value="Cargar archivo seleccionado">
                        <button type="button" class="boton descargar">DESCARGAR DATOS CDP</button>
                    </div>
                </form>
            </div>

            <!-- Formulario CRP -->
            <h1>Seleccionar archivo CRP</h1>
            <div class="conten-form_csv">
                <form action="control/procesarCRP.php" method="post" enctype="multipart/form-data" onsubmit="return validateCRPFile()">
                    <input type="file" name="dataCRP" id="file-input-crp" style="display: none;" onchange="displayCRPFileName()">
                    <label for="file-input-crp" class="file-input-label">
                        <i class="zmdi zmdi-upload zmdi-hc-2x"></i>
                        <span id="file-name-crp">Elegir Archivo CSV</span>
                    </label>
                    <div class="botones">
                        <input type="submit" class="boton actualizar" value="Cargar archivo seleccionado">
                        <button type="button" class="boton descargar">DESCARGAR DATOS CRP</button>
                    </div>
                </form>
            </div>

            <!-- Formulario OP -->
            <h1>Seleccionar archivo OP</h1>
            <div class="conten-form_csv">
                <form action="control/procesarOP.php" method="post" enctype="multipart/form-data" onsubmit="return validateOPFile()">
                    <input type="file" name="dataop" id="file-input-op" style="display: none;" onchange="displayOPFileName()">
                    <label for="file-input-op" class="file-input-label">
                        <i class="zmdi zmdi-upload zmdi-hc-2x"></i>
                        <span id="file-name-op">Elegir Archivo CSV</span>
                    </label>
                    <div class="botones">
                        <input type="submit" class="boton actualizar" value="Cargar archivo seleccionado">
                        <button type="button" class="boton descargar">DESCARGAR DATOS OP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php 
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php';
    ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const overlay = document.getElementById('loadingOverlay');
    const mainContent = document.getElementById('mainContent');
    
    setTimeout(() => {
        overlay.style.display = 'none';
        
        mainContent.classList.add('loaded');
    }, 2000);
});
document.getElementById('viewToggle').addEventListener('change', function() {
    const usuariosView = document.getElementById('usuariosView');
    const candidatosView = document.getElementById('candidatosView');
    
    if (this.checked) {
        usuariosView.style.display = 'none';
        candidatosView.style.display = 'block';
    } else {
        usuariosView.style.display = 'block';
        candidatosView.style.display = 'none';
    }
});


$('.filtro-btn').click(function() {
    $(this).toggleClass('active');
});

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modalActualizar');
    const openModalButton = document.getElementById('openModalButton');
    const closeModalButton = document.getElementById('closeModalButton');

    //Abrir el modal
    openModalButton.addEventListener('click', function() {
        modal.style.display = 'block';
    });

    //Cerrar el modal
    closeModalButton.addEventListener('click', function() {
        modal.style.display = 'none';
    });

    //Cerrar el modal si se hace clic fuera del contenido del modal
    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});

$(document).ready(function() {
    const overlayEliminar = $('#overlay-eliminar');
    const modalEliminar = $('#modal-eliminar');
    const overlayConfirmacion = $('#overlay-confirmacion');
    const modalConfirmacion = $('#modal-confirmacion');
    
    let usuarioIdEliminar = null;

    function openModal(id) {
        overlayEliminar.show();
        modalEliminar.show();
        usuarioIdEliminar = id; 
    }
    function closeModal() {
        overlayEliminar.hide();
        modalEliminar.hide();
        usuarioIdEliminar = null; 
    }
    function openConfirmationModal() {
        overlayConfirmacion.show();
        modalConfirmacion.show();
    }
    function closeConfirmationModal() {
        overlayConfirmacion.hide();
        modalConfirmacion.hide();
    }

    function confirmDelete() {
        if (usuarioIdEliminar) {
            console.log(`Eliminando usuario con documento: ${usuarioIdEliminar}`); // Depuración
            fetch(`<?php echo rtrim(BASE_URL, '/'); ?>/app/admin/control/eliminar.php?numero=${usuarioIdEliminar}`)
                .then(response => {
                    console.log('Respuesta recibida:', response); // Depuración
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.text(); // Cambiar a text() para depuración
                })
                .then(text => {
                    console.log('Texto recibido:', text); // Depuración
                    let jsonText = text.substring(text.indexOf('{')); // Limpiar texto antes del JSON
                    let data;
                    try {
                        data = JSON.parse(jsonText); // Intentar parsear el texto como JSON
                    } catch (error) {
                        throw new Error('Respuesta no es un JSON válido: ' + text);
                    }
                    console.log('Datos parseados:', data); // Depuración
                    // Mostrar el mensaje en el modal de confirmación
                    if (data.success) {
                        $('#modal-message-confirmacion').text('El usuario ha sido eliminado exitosamente.');
                        closeModal();
                        openConfirmationModal();
                        setTimeout(() => {
                            closeConfirmationModal();
                            cargarUsuarios(); // Actualizar la tabla de usuarios
                        }, 3000);
                    } else {
                        $('#modal-message-confirmacion').text('Error al eliminar el usuario' + (data.error ? `: ${data.error}` : ''));
                        openConfirmationModal();
                    }
                })
                .catch(error => {
                    console.error('Error al procesar la solicitud:', error); // Depuración
                    $('#modal-message-confirmacion').text('Error al procesar la solicitud: ' + error.message);
                    openConfirmationModal();
                });
        }
    }

    // Delegación de eventos para los botones de eliminar
    $(document).on('click', '.eliminar-btn', function(event) {
        event.preventDefault(); 
        const userId = $(this).data('numero'); 
        openModal(userId); 
    });

    // Manejadores de eventos para los botones del modal
    $('.confirm-button-eliminar').on('click', confirmDelete);
    $('.cancel-button-eliminar').on('click', closeModal);
    $('.confirm-button-confirmacion').on('click', function() {
        closeConfirmationModal();
        cargarUsuarios(); // Actualizar la tabla de usuarios
    });

    // Cerrar modales al hacer clic fuera de ellos
    $(window).on('click', function(event) {
        if (event.target === overlayEliminar[0]) {
            closeModal();
        }
        if (event.target === overlayConfirmacion[0]) {
            closeConfirmationModal();
        }
    });

    // Manejador de búsqueda en tiempo real
    $('#buscar').on('input', function() {
        let busqueda = $(this).val();
        cargarUsuarios(busqueda);
    });

    // Función para cargar los usuarios mediante AJAX
    function cargarUsuarios(busqueda = '') {
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: { busqueda: busqueda },
            dataType: 'json',
            success: function(data) {
                let html = '';
                if (data.length === 0) {
                    html = '<tr><td colspan="9" style="text-align:center;">No se encontraron resultados.</td></tr>';
                } else {
                    data.forEach(function(usuario) {
                        html += `
                            <tr data-numero="${usuario.numero_documento}">
                                <td>${usuario.id_rol}</td>
                                <td>${usuario.numero_documento}</td>
                                <td>${usuario.tipo_doc}</td>
                                <td>${usuario.nombre_completo}</td>
                                <td>${usuario.email}</td>
                                <td>${usuario.telefono}</td>
                                <td>${usuario.nombre_rol}</td>
                                <td style="text-align: center;">
                                    <a href="<?php echo BASE_URL; ?>app/admin/control/formEditarUsuario.php?numero=${usuario.numero_documento}">
                                        <button class="editar-btn">&#9998;</button>
                                    </a>
                                </td>
                                <td style="text-align: center;">
                                    <button class="eliminar-btn" data-numero="${usuario.numero_documento}">Eliminar</button>
                                </td>
                            </tr>
                        `;
                    });
                }
                $('#tablaUsuarios').html(html);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error('Error:', textStatus, errorThrown);
                $('#tablaUsuarios').html('<tr><td colspan="9" style="text-align:center;">Error al cargar los datos.</td></tr>');
            }
        });
    }

    // Cargar datos iniciales
    cargarUsuarios();
});

$(document).ready(function() {
    // Variables globales para controlar el estado de filtrado
    let filtrandoPorVacante = false;
    let candidatosActuales = [];

    // Función para cargar candidatos
    function cargarCandidatos(busqueda = '') {
        if (filtrandoPorVacante) {
            // Si estamos filtrando por vacante, filtrar entre los candidatos actuales
            let resultados = candidatosActuales.filter(function(candidato) {
                return candidato.numero_documento.includes(busqueda) || 
                       candidato.nombre_completo.toLowerCase().includes(busqueda.toLowerCase());
            });
            actualizarTablaCandidatos(resultados);
        } else {
            // Si no, realizar una solicitud AJAX al servidor
            $.ajax({
                url: window.location.href,
                method: 'GET',
                data: { busquedaC: busqueda },
                dataType: 'json',
                success: function(data) {
                    candidatosActuales = data; // Guardamos los candidatos actuales
                    actualizarTablaCandidatos(data);
                    console.log(data);
                }
            });
        }
    }
    // Función para actualizar la tabla de candidatos
    function actualizarTablaCandidatos(data) {
        let html = '';
        if (data.length > 0) {
            data.forEach(function(candidato, index) {
                html += `
                    <tr id="row-${index}">
                        <td>${candidato.cod_vacante || ''}</td>
                        <td>${candidato.numero_documento}</td>
                        <td>${candidato.nombre_completo}</td>
                        <td>${candidato.estadoBANIN}</td>
                        <td>${candidato.coordinacion_inicial_nombre || ''}</td>
                        <td>${candidato.coordinacion_final_nombre || ''}</td>
                        <td>${candidato.traslado ? candidato.traslado : 'Esperando confirmación.'}</td>
                        <td>${candidato.reclamacion || ''}</td>
                        <td>${candidato.proteccion || ''}</td>
                        <td style="text-align: center;">
                            <a href="../administrador/traslado.php?documento=${candidato.numero_documento}">
                                <button class="editar-btn">&#9998;</button>
                            </a>
                        </td>
                        <td style="text-align: center;">
                            <a href="../administrador/gestionTraslados.php?documento=${candidato.numero_documento}">
                                <button class="editar-btn"><i class="fas fa-search icono-blanco"></i></button>
                            </a>
                        </td>
                    </tr>
                `;
            });
        } else {
            html = '<tr><td colspan="10">No hay candidatos que coincidan con la búsqueda.</td></tr>';
        }
        $('#tablaCandidatos').html(html);
    }

    // Cargar candidatos inicialmente
    cargarCandidatos();

    // Evento para el buscador por nombre o documento
    $('#buscarC').on('input', function() {
        let busqueda = $(this).val();
        cargarCandidatos(busqueda);
    });



    // Evento para el botón 'Ver'
    $('#btnVer').on('click', function() {
        let codVacante = $('#buscar_vacante').val().trim();
    
        // Validar que el campo no esté vacío
        if (codVacante === '') {
            alert('Por favor, ingrese un código de vacante válido.');
            return;
        }
    
        // Enviar solicitud AJAX al servidor
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: { codigo_vacante: codVacante },
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    // Guardar que estamos filtrando por vacante
                    filtrandoPorVacante = true;
                    candidatosActuales = data; // Guardar los candidatos actuales
                    // Actualizar la tabla con los candidatos de la vacante
                    actualizarTablaCandidatos(data);
                } else {
                    // Mostrar mensaje de que no existe la vacante o no tiene permiso
                    $('#tablaCandidatos').html('<tr><td colspan="10">No existe una vacante con ese código o no tiene permiso para verla.</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', error);
                alert('Error al buscar la vacante. Intente nuevamente.');
            }
        });
    });


    // Evento para el botón 'Limpiar'
    $('#btnLimpiar').on('click', function() {
        $('#buscar_vacante').val('');
        $('#buscar').val('');
        filtrandoPorVacante = false;
        candidatosActuales = [];
        cargarCandidatos(); // Cargar los candidatos iniciales
    });

    $('.filtro-btn').click(function() {
        $(this).toggleClass('active');
    });


    function updateCoordinacionFinalOptions(coordinacionInicial) {
        const coordinaciones = ['SER', 'TITULADA', 'ARTICULACIÓN', 'COMPLEMENTARIA'];
        const $coordinacionFinal = $('#coordinacionFinal');
        
        //Variable de configuración: Cambia a 'false' para permitir seleccionar la misma coordinación Al contrario true
        const excludeInitialCoordination = false;

        $coordinacionFinal.empty().append('<option value="">Seleccione una opción</option>');
        
        coordinaciones.forEach(coord => {
            if (!excludeInitialCoordination || coord !== coordinacionInicial) {
                $coordinacionFinal.append(`<option value="${coord}">${coord}</option>`);
            }
        });
    }


    function obtenerCodigosDisponibles(coordinacion) {
        $.ajax({
            url: window.location.href,
            method: 'GET',
            data: { coordinacion: coordinacion },
            dataType: 'json',
            success: function(codigos) {
                const $codigoTraslado = $('#codigoTraslado');
                $codigoTraslado.empty().append('<option value="">Seleccione un código</option>');
                
                codigos.forEach(codigo => {
                    $codigoTraslado.append(`<option value="${codigo}">${codigo}</option>`);
                });
            },
            error: function() {
                console.error('Error al obtener códigos');
                $('#codigoTraslado')
                    .prop('disabled', true)
                    .html('<option value="">Error al cargar códigos</option>');
            }
        });
    }

});

function validateCRPFile() {
    const fileInput = document.getElementById('file-input-crp');
    if (!fileInput.files || fileInput.files.length === 0) {
        alert('Por favor seleccione un archivo CSV');
        return false;
    }
    const file = fileInput.files[0];
    if (!file.name.toLowerCase().endsWith('.csv')) {
        alert('Por favor seleccione un archivo CSV válido');
        return false;
    }
    return true;
}

function displayCRPFileName() {
    const fileInput = document.getElementById('file-input-crp');
    const fileName = document.getElementById('file-name-crp');
    if (fileInput.files && fileInput.files.length > 0) {
        fileName.textContent = fileInput.files[0].name;
    }
}

// Funciones de validación para CDP
function validateCDPFile() {
    const fileInput = document.getElementById('file-input-cdp');
    return validateFile(fileInput, 'CDP');
}

function displayCDPFileName() {
    displayFileName('file-input-cdp', 'file-name-cdp');
}

// Funciones de validación para OP
function validateOPFile() {
    const fileInput = document.getElementById('file-input-op');
    return validateFile(fileInput, 'OP');
}

function displayOPFileName() {
    displayFileName('file-input-op', 'file-name-op');
}

// Funciones genéricas
function validateFile(fileInput, type) {
    if (!fileInput.files || fileInput.files.length === 0) {
        alert(`Por favor seleccione un archivo CSV para ${type}`);
        return false;
    }
    const file = fileInput.files[0];
    if (!file.name.toLowerCase().endsWith('.csv')) {
        alert(`Por favor seleccione un archivo CSV válido para ${type}`);
        return false;
    }
    return true;
}

function displayFileName(inputId, spanId) {
    const fileInput = document.getElementById(inputId);
    const fileName = document.getElementById(spanId);
    if (fileInput.files && fileInput.files.length > 0) {
        fileName.textContent = fileInput.files[0].name;
    }
}
</script>
</body>
</html>