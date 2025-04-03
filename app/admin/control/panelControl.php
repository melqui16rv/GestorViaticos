<?php

ob_start();


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';

requireRole(['1']);


$miClase = new user();
if (isset($_GET['busqueda'])) {
    $termino = $_GET['busqueda'];
    $datos = $miClase->buscar_usuario($termino);

    // Limpia cualquier salida previa
    ob_clean();

    // Devolver JSON y salir
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/links/usuariosAdmin.css">
    <link rel="stylesheet" href="../../assets/css/shareInFolder/styleTabla.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Overlay */
        #overlay-eliminar, #overlay-confirmacion {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1000;
        }

        /* Modal container */
        #modal-eliminar, #modal-confirmacion {
            display: none; /* Asegura que los modals estén ocultos por defecto */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 300px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1001;
            text-align: center;
        }

        /* Modal header */
        #modal-header-eliminar, #modal-header-confirmacion {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        /* Modal message */
        #modal-message-eliminar, #modal-message-confirmacion {
            font-size: 14px;
            margin-bottom: 20px;
        }

        /* Modal buttons */
        .modal-button-eliminar, .modal-button-confirmacion {
            display: inline-block;
            padding: 8px 16px;
            font-size: 14px;
            margin: 5px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
        }

        .confirm-button-eliminar {
            background-color: #d9534f;
            color: white;
        }

        .cancel-button-eliminar, .confirm-button-confirmacion {
            background-color: #5bc0de;
            color: white;
        }
        #crearVacanteForm {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin: auto;
        }

        #crearVacanteForm h3 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        #crearVacanteForm label {
            display: block;
            font-weight: bold;
            margin: 10px 0 5px;
            color: #333;
        }

        #crearVacanteForm input[type="text"],
        #crearVacanteForm input[type="number"],
        #crearVacanteForm input[type="date"],
        #crearVacanteForm select,
        #crearVacanteForm textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 16px;
            color: #333;
            box-sizing: border-box;
        }

        #crearVacanteForm textarea {
            resize: vertical;
            min-height: 80px;
            max-height: 30px;
        }

        #crearVacanteForm input[type="submit"],
        #cancelarFormulario {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        #crearVacanteForm input[type="submit"] {
            background-color: #28a745;
            color: white;
        }

        #cancelarFormulario {
            background-color: #dc3545;
            color: white;
            margin-top: 10px;
        }
        #formulario {
            display: none;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 1200px;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        #formulario h3 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.5em;
        }

        /* Estilos de columnas responsivas */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        .control-form{
            width:100%;
        }

        .form-group {
            flex: 1 1 calc(25% - 10px); /* Ocupa el 33.33% del ancho en pantallas grandes */
            display: flex;
            flex-direction: column;
        }

        label {
            font-size: 0.9em;
            color: #333;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea,
        select {
            padding: 8px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        @media (min-width: 1600px) {
            .form-group {
                flex: 1 1 calc(20% - 10px); /* Ocupa el 20% del ancho en pantallas muy grandes para cinco columnas */
            }
        }
        /* Ajustes para pantallas muy grandes */
        @media (min-width: 1200px) {
            .form-group {
                flex: 1 1 calc(25% - 10px); /* Ocupa el 25% del ancho en pantallas extra grandes */
            }
        }

        /* Ajustes para pantallas pequeñas */
        @media (max-width: 768px) {
            .form-group {
                flex: 1 1 100%;
            }
        }

        /* Botones */
        input[type="submit"],
        #cancelarFormulario {
            width: 50%;
            padding: 10px;
            font-size: 1em;
            cursor: pointer;
        }

        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
        }

        #cancelarFormulario {
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        /* Contenedor principal */
.conten-form_csv {
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
}

/* Título */
.conten-form_csv h1 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
}

/* Estilo para el input de archivo */
.file-input-label {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    color: #fff;
    background-color: #007bff;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-bottom: 15px;
}

.file-input-label:hover {
    background-color: #0056b3;
}



.boton {
    display: inline-block;
    padding: 10px 15px;
    font-size: 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin: 5px;
}

.boton.actualizar {
    background-color: #28a745;
    color: white;
}

.boton.actualizar:hover {
    background-color: #218838;
}

.boton.descargar {
    background-color: #17a2b8;
    color: white;
}

.boton.descargar:hover {
    background-color: #138496;
}
.modal-content p {
  color: #666;
  margin-bottom: 0px;
}

    </style>
</head>
<body>
    <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php';
    ?>
<!-- overlay -->
    <div id="loadingOverlay" class="overlay">
        <div class="spinner-container">
            <div class="spinner"></div>
        </div>
    </div>

<!-- content -->
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
            <div class="buscador">
                <br>
                <h2 class="titulo">Gestion de Traslados</h2><br>
                <h3 class="titulo" style="border-bottom: 2px solid;    border-color: #388a7e;    width: 300px;">Buscar</h3>
                <div class="formBusqueda">
                    <input type="text" id="buscarC" name="buscarC" class="codigo" 
                           placeholder="Buscar por documento o nombre">
                    <input type="text" id="buscar_vacante" name="buscar_vacante" class="codigo" placeholder="Ingrese código de vacante">
                    <div class="lim">
                        <button id="btnVer" class="busqueda-btn" type="button"><i class="fas fa-search"></i></button>
                        <button id="btnLimpiar" class="busqueda-btn" type="button"><i class="fas fa-broom"></i>
                        </button>
                    </div>
                </div>
            </div>
            <br>
            <h2>Resultados de la Consulta</h2>
            <div class="tablaGeneradaPorLaConsulta">
                
                <table>
                    <thead>
                        <tr>
                            <th class="border_left">Código</th>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Estado BANIN</th>
                            <th>Coordinación Inicial</th>
                            <th>Coordinación Final</th>
                            <th>Traslado</th>
                            <th>Reclamación</th>
                            <th>Protección</th>
                            <th>Solicitud</th>
                            <th class="border_right" style="text-align: center;">Gestion</th>
                        </tr>
                    </thead>

                    <tbody id="tablaCandidatos">

                    </tbody>
                </table>
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
</script>


<script>
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
</script>

<script>
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
</script>
</body>
</html>