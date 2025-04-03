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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/gestor/insert.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/botonRetrocedar.css">
</head>
<body class="filament-body">
    <div class="filament-return-button-container">
        <button type="button" class="filament-button filament-button-secondary filament-return-button" onclick="window.history.back();">
            ← Volver
        </button>
    </div>

    <div class="filament-card">
        <h2 class="filament-card-title">Registrar asignación de viático</h2>
        <form action="procesar_saldo.php" method="POST" class="filament-form">
            <div class="filament-form-group">
                <label for="codigo_cdp_visible" class="filament-form-label">Código CDP</label>
                <input type="text" id="codigo_cdp_visible" name="codigo_cdp_visible" readonly class="filament-form-input">
                <input type="hidden" id="codigo_cdp" name="codigo_cdp" required>
            </div>

            <div class="filament-form-group">
                <label for="codigo_crp_visible" class="filament-form-label">Código RP</label>
                <input type="text" id="codigo_crp_visible" name="codigo_crp_visible" readonly class="filament-form-input">
                <input type="hidden" id="codigo_crp" name="codigo_crp" required>
            </div>

            <div class="filament-form-group">
                <label for="nombre" class="filament-form-label">Nombre (Persona asignada al viático)</label>
                <input type="text" id="nombre" name="nombre" required class="filament-form-input">
            </div>

            <div class="filament-form-group">
                <label for="documento" class="filament-form-label">N° Documento (Persona asignada al viático):</label>
                <input type="text" id="documento" name="documento" required class="filament-form-input">
            </div>

            <div class="filament-form-group">
                <label for="fecha_inicio" class="filament-form-label">Fecha Inicio (Inicio del evento a viaticar):</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required class="filament-form-input">
            </div>

            <div class="filament-form-group">
                <label for="fecha_fin" class="filament-form-label">Fecha Fin (Inicio del evento a viaticar):</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required class="filament-form-input">
            </div>

            <div class="filament-form-group">
                <label for="fecha_pago" class="filament-form-label">Fecha Pago (opcional) (Fecha planeada para trasferir el saldo a la persona viaticada):</label>
                <input type="date" id="fecha_pago" name="fecha_pago" class="filament-form-input">
            </div>

            <div class="filament-form-group">
                <label for="saldo_asignado" class="filament-form-label">Saldo Asignado (Monto total ha asignar):</label>
                <input type="number" step="0.01" id="saldo_asignado" name="saldo_asignado" required class="filament-form-input">
            </div>

            <div class="filament-form-actions">
                <button type="submit" class="filament-button filament-button-primary">Guardar registro de asignación</button>
            </div>
        </form>
    </div>

    <div class="filament-card">
        <h3 class="filament-card-title">Seleccionar CDP (VIATICOS)</h3>
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
                        <!-- Puedes agregar más campos aquí según necesites -->
                    </tr>
                </thead>
                <tbody class="filament-table-body">
                    <?php foreach ($cdps as $cdp): ?>
                        <tr class="filament-table-row">
                            <td class="filament-table-cell">
                                <button type="button" class="filament-button filament-button-secondary"
                                        onclick="seleccionarCDP('<?php echo htmlspecialchars($cdp['CODIGO_CDP']); ?>',
                                                           '<?php echo htmlspecialchars($cdp['Numero_Documento']); ?>',
                                                           this)">
                                    Seleccionar
                                </button>
                            </td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Numero_Documento']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Objeto']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Dependencia']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Rubro']); ?></td>
                            <td class="filament-table-cell"><?php echo htmlspecialchars($cdp['Fuente']); ?></td>
                            <!-- Puedes agregar más campos aquí según necesites -->
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="filament-text-sm"><strong>CDP Seleccionado:</strong> <span id="numeroDocumentoSeleccionado">Ninguno</span></p>
    </div>

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
                        <td class="filament-table-cell">${crp.Saldo_por_Utilizar}</td>
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
</script>
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