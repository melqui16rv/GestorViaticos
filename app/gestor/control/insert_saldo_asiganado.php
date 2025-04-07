<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/crpAsociados.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/metodosGestor.php';

requireRole(['2']);

$gestor = new gestor1();
$cdps = $gestor->obtenerCDPsViaticos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Asignación de viático</title>
    <!-- Estilos existentes -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/gestor/insert.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/botonRetrocedar.css">
    <!-- NUEVO: vincula tu archivo CSS externo para Drag & Drop (ej: drag-drop.css) -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/gestor/drag-drop.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="filament-body">
    <div class="filament-return-button-container">
        <button type="button" class="filament-button filament-button-secondary filament-return-button" onclick="window.history.back();">
            ← Volver
        </button>
    </div>

    <div class="filament-card">
        <h2 class="filament-card-title">Registrar asignación de viático</h2>

        <!-- IMPORTANTE: enctype="multipart/form-data" para permitir subir archivos -->
        <form action="procesar_saldo.php" method="POST" enctype="multipart/form-data" class="filament-form">

            <!-- Código CDP -->
            <div class="filament-form-group">
                <label for="codigo_cdp_visible" class="filament-form-label">Código CDP</label>
                <input type="text" id="codigo_cdp_visible" name="codigo_cdp_visible" readonly class="filament-form-input">
                <input type="hidden" id="codigo_cdp" name="codigo_cdp" required>
            </div>

            <!-- Código RP -->
            <div class="filament-form-group">
                <label for="codigo_crp_visible" class="filament-form-label">Código RP</label>
                <input type="text" id="codigo_crp_visible" name="codigo_crp_visible" readonly class="filament-form-input">
                <input type="hidden" id="codigo_crp" name="codigo_crp" required>
            </div>

            <!-- Nombre -->
            <div class="filament-form-group">
                <label for="nombre" class="filament-form-label">Nombre (Persona asignada al viático)</label>
                <input type="text" id="nombre" name="nombre" required class="filament-form-input">
            </div>

            <!-- Documento -->
            <div class="filament-form-group">
                <label for="documento" class="filament-form-label">N° Documento (Persona asignada al viático):</label>
                <input type="text" id="documento" name="documento" required class="filament-form-input">
            </div>

            <!-- Fecha Inicio -->
            <div class="filament-form-group">
                <label for="fecha_inicio" class="filament-form-label">Fecha Inicio (Inicio del evento a viaticar):</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required class="filament-form-input">
            </div>

            <!-- Fecha Fin -->
            <div class="filament-form-group">
                <label for="fecha_fin" class="filament-form-label">Fecha Fin (Fin del evento a viaticar):</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required class="filament-form-input">
            </div>

            <!-- Fecha Pago -->
            <div class="filament-form-group">
                <label for="fecha_pago" class="filament-form-label">Fecha Pago (opcional) (Fecha planeada para trasferir el saldo a la persona viaticada):</label>
                <input type="date" id="fecha_pago" name="fecha_pago" class="filament-form-input">
            </div>

            <!-- Saldo Asignado (con formateo en tiempo real) -->
            <div class="filament-form-group">
                <label for="saldo_asignado_visible" class="filament-form-label">Saldo Asignado (Monto total a asignar):</label>
                <input type="text" id="saldo_asignado_visible" class="filament-form-input" placeholder="$0,00">
                <input type="hidden" id="saldo_asignado" name="saldo_asignado" required>
            </div>

            <!-- Bloque Drag & Drop para la Imagen "Visto Bueno Subdirector" -->
            <div class="filament-form-group">
                <div class="drag-drop-container">
                    <label class="drag-drop-label" for="mi_imagen">
                        Visto Bueno Subdirector (opcional):
                    </label>
                    
                    <!-- Input file real (oculto) -->
                    <input
                        type="file"
                        id="mi_imagen"
                        name="mi_imagen"
                        accept="image/*"
                        class="drag-drop-file-input"
                        onchange="validarArchivoSeleccionado(event)"
                    />

                    <!-- Botón que simula "Examinar" -->
                    <label for="mi_imagen" class="drag-drop-button">
                        Examinar
                    </label>

                    <!-- Área para arrastrar y soltar (Drag & Drop) -->
                    <div 
                        id="drop-area" 
                        class="drag-drop-area"
                        ondragenter="dragEnter(event)"
                        ondragover="dragOver(event)"
                        ondragleave="dragLeave(event)"
                        ondrop="dropFile(event)"
                    >
                        <p id="drag-drop-text">Arrastra tu imagen aquí o haz click en “Examinar”</p>
                    </div>

                    <!-- Texto de retroalimentación -->
                    <span id="drag-drop-feedback" class="drag-drop-feedback">
                        No se ha seleccionado ninguna imagen
                    </span>
                </div>
            </div>

            <!-- Botón para enviar el formulario -->
            <div class="filament-form-actions">
                <button type="submit" class="filament-button filament-button-primary">
                    Guardar registro de asignación
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de CDP -->
    <div class="filament-card">
        <div class="header-container">
            <h3 class="filament-card-title">Seleccionar CDP (VIATICOS)</h3>
            <div class="search-container">
                <input type="text" id="searchCDP" placeholder="Buscar CDP..." class="search-input">
            </div>
        </div>
        <div class="filament-table-container">
            <table class="filament-table" id="tablaCDPSeleccion">
                <thead class="filament-table-header">
                    <tr>
                        <th class="filament-table-heading">Seleccionar</th>
                        <th class="filament-table-heading">Número CDP</th>
                        <th class="filament-table-heading">Objeto</th>
                        <th class="filament-table-heading">Dependencia</th>
                        <th class="filament-table-heading">Rubro</th>
                        <th class="filament-table-heading">Fuente</th>
                    </tr>
                </thead>
                <tbody class="filament-table-body">
                    <?php foreach ($cdps as $cdp): ?>
                        <tr class="filament-table-row">
                            <td class="filament-table-cell">
                                <button 
                                    type="button" 
                                    class="filament-button filament-button-secondary"
                                    onclick="seleccionarCDP(
                                        '<?php echo htmlspecialchars($cdp['CODIGO_CDP']); ?>',
                                        '<?php echo htmlspecialchars($cdp['Numero_Documento']); ?>',
                                        this
                                    )">
                                    Seleccionar
                                </button>
                            </td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Numero_Documento']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Objeto']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Dependencia']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Rubro']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Fuente']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="filament-text-sm">
            <strong>CDP Seleccionado:</strong> 
            <span id="numeroDocumentoSeleccionado">Ninguno</span>
        </p>
    </div>

    <!-- Tabla de CRP -->
    <div class="filament-card">
        <h3 class="filament-card-title">Seleccionar RP asociado al CDP seleccionado</h3>
        <div class="filament-table-container">
            <table class="filament-table" id="tablaCRPSeleccion">
                <thead class="filament-table-header">
                    <tr>
                        <th class="filament-table-heading">Seleccionar</th>
                        <th class="filament-table-heading">Número CRP</th>
                        <th class="filament-table-heading">Descripción</th>
                        <th class="filament-table-heading">Saldo Disponible</th>
                        <th class="filament-table-heading">Compromiso</th>
                        <th class="filament-table-heading">Obligación</th>
                    </tr>
                </thead>
                <tbody class="filament-table-body"></tbody>
            </table>
        </div>
    </div>

    <!-- Scripts de selección de CDP y CRP -->
    <script>
    function seleccionarCDP(codigoCDP, numeroDocumento, boton) {
        // Marcar la fila seleccionada visualmente
        document.querySelectorAll('#tablaCDPSeleccion .filament-table-row').forEach(row => {
            row.classList.remove('active');
        });
        boton.closest('.filament-table-row').classList.add('active');

        // Asignar valores a campos CDP
        document.getElementById('codigo_cdp').value = codigoCDP;
        document.getElementById('codigo_cdp_visible').value = codigoCDP;
        document.getElementById('numeroDocumentoSeleccionado').textContent = numeroDocumento;

        // Reset de CRP
        document.getElementById('codigo_crp').value = '';
        document.getElementById('codigo_crp_visible').value = '';
        document.querySelector('#tablaCRPSeleccion tbody').innerHTML = '';

        // Cargar CRPs relacionados
        fetch('<?php echo BASE_URL; ?>/math/gestor/obtenerCRPs.php?codigo_cdp=' + encodeURIComponent(codigoCDP))
            .then(response => response.json())
            .then(data => {
                const tbody = document.querySelector('#tablaCRPSeleccion tbody');
                tbody.innerHTML = '';

                if (data.length === 0) {
                    tbody.innerHTML = '<tr><td class="filament-table-cell" colspan="6">No se encontraron CRPs para este CDP</td></tr>';
                    return;
                }

                data.forEach(crp => {
                    const saldoFormateado = new Intl.NumberFormat('es-CO', {
                        style: 'currency',
                        currency: 'COP',
                        minimumFractionDigits: 2
                    }).format(crp.Saldo_por_Utilizar);

                    const row = `
                        <tr class="filament-table-row">
                            <td class="filament-table-cell">
                                <button type="button" class="filament-button filament-button-secondary"
                                        onclick="seleccionarCRP('${crp.CODIGO_CRP}', this)">
                                    Seleccionar
                                </button>
                            </td>
                            <td class="filament-table-cell">${crp.Numero_Documento}</td>
                            <td class="filament-table-cell">${crp.Observaciones}</td>
                            <td class="filament-table-cell">${saldoFormateado}</td>
                            <td class="filament-table-cell">${crp.Compromisos}</td>
                            <td class="filament-table-cell">${crp.Obligaciones}</td>
                        </tr>`;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => {
                console.error('Error al obtener CRPs:', error);
            });
    }

    function seleccionarCRP(codigoCRP, boton) {
        document.getElementById('codigo_crp').value = codigoCRP;
        document.getElementById('codigo_crp_visible').value = codigoCRP;

        // Marcar fila seleccionada en la tabla de CRPs
        document.querySelectorAll('#tablaCRPSeleccion .filament-table-row').forEach(row => {
            row.classList.remove('active');
        });
        boton.closest('.filament-table-row').classList.add('active');
    }

    // Formateo de moneda en tiempo real
    const inputVisible = document.getElementById('saldo_asignado_visible');
    const inputHidden = document.getElementById('saldo_asignado');

    inputVisible.addEventListener('input', (e) => {
        // Valor sin caracteres no numéricos
        let value = e.target.value.replace(/[^0-9]/g, '');

        // Actualizar el valor del input oculto (sin formato)
        inputHidden.value = parseFloat(value) / 100;

        // Formatear el valor como moneda
        e.target.value = new Intl.NumberFormat('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 2
        }).format(value / 100);
    });
    </script>

    <!-- Drag & Drop y validaciones de la imagen -->
    <script>
    const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];

    // Cuando el usuario selecciona la imagen con “Examinar”
    function validarArchivoSeleccionado(event) {
        const input = event.target;  // El <input type="file">
        const file = input.files[0];
        if (!file) {
            mostrarMensaje("No se ha seleccionado ninguna imagen");
            return;
        }
        validarImagen(file);
    }

    // Valida que sea un archivo de imagen (extensión + tipo MIME)
    function validarImagen(file) {
        const fileExtension = file.name.split('.').pop().toLowerCase();

        // 1) Validación de MIME
        if (!file.type.startsWith("image/")) {
            alert("Por favor selecciona un archivo de imagen válido (JPG, PNG, GIF, BMP).");
            limpiarSeleccion();
            return;
        }

        // 2) Validación por extensión
        if (!ALLOWED_EXTENSIONS.includes(fileExtension)) {
            alert("Solo se permiten archivos .jpg, .jpeg, .png, .gif, .bmp");
            limpiarSeleccion();
            return;
        }

        // Si todo sale bien, mostramos el nombre del archivo
        mostrarMensaje("Imagen seleccionada: " + file.name);
    }

    // Limpia el input y el feedback
    function limpiarSeleccion() {
        const input = document.getElementById('mi_imagen');
        input.value = "";
        mostrarMensaje("No se ha seleccionado ninguna imagen");
    }

    function mostrarMensaje(texto) {
        document.getElementById('drag-drop-feedback').textContent = texto;
    }

    /* MANEJO DE EVENTOS DRAG & DROP */

    // Se dispara cuando arrastran un archivo dentro del área
    function dragEnter(event) {
        event.preventDefault();
        const dropArea = document.getElementById('drop-area');
        dropArea.classList.add('drag-drop-area-hover');
    }

    // Se dispara al arrastrar sobre el área (evita abrir archivo en el navegador)
    function dragOver(event) {
        event.preventDefault();
    }

    // Se dispara cuando el archivo sale del área
    function dragLeave(event) {
        const dropArea = document.getElementById('drop-area');
        dropArea.classList.remove('drag-drop-area-hover');
    }

    // Se dispara cuando se suelta el archivo en el área
    function dropFile(event) {
        event.preventDefault();
        const dropArea = document.getElementById('drop-area');
        dropArea.classList.remove('drag-drop-area-hover');

        const files = event.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            // Asignamos el archivo al input "mi_imagen" para que se envíe con el formulario
            document.getElementById('mi_imagen').files = files;
            // Validar que sea imagen
            validarImagen(file);
        }
    }
    </script>

    <!-- Buscador en tiempo real para la tabla CDP -->
    <script>
    $(document).ready(function() {
        $('#searchCDP').on('keyup', function() {
            const searchText = $(this).val().toLowerCase();
            $('#tablaCDPSeleccion tbody tr').each(function() {
                const cdpNumero = $(this).find('td:eq(1)').text().toLowerCase();
                $(this).toggle(cdpNumero.includes(searchText));
            });
        });
    });
    </script>

    <!-- Mensajes de éxito / error al procesar_saldo.php -->
    <?php if (isset($_GET['estado'])): ?>
    <script>
        <?php if ($_GET['estado'] === 'exito'): ?>
            alert("¡Registro exitoso! La asignación del viático se ha guardado correctamente.");
            window.location.href = "../index.php";
        <?php elseif ($_GET['estado'] === 'error'): ?>
            alert("Error al registrar la asignación del viático. Intente nuevamente.");
        <?php endif; ?>
    </script>
    <?php endif; ?>

</body>
</html>