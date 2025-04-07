<?php
session_start();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gen/user.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/math/gestor/metodosGestor.php';

requireRole(['2']);
$miClaseG = new gestor();

$idSaldo = $_GET['id_saldo'] ?? null;

if (!$idSaldo) {
    die("ID de saldo no proporcionado.");
}

$detalleSaldo = $miClaseG->obtenerDetalleSaldo($idSaldo);

if (!$detalleSaldo) {
    die("No se encontró información para el ID proporcionado.");
}

$detalleCDP = $miClaseG->obtenerDetalleCDP($detalleSaldo['CODIGO_CDP'], '*');
$detalleCRP = $miClaseG->obtenerDetalleCRP($detalleSaldo['CODIGO_CRP'], '*');

// Inicializar la variable $imagenes
$imagenes = $miClaseG->obtenerImagenesDeSaldo($idSaldo) ?? [];
?>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Registro</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="icon" href="<?php echo BASE_URL; ?>assets/img/public/logosena.png">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/gestor/insert.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/share/botonRetrocedar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/gestor/asignacion.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/nav.php'; ?>
    <div class="filament-return-button-container">
            <button type="button" class="filament-button filament-button-secondary filament-return-button" onclick="window.history.back();">
                ← Volver
            </button>
    </div>
    <div class="contenedorAsignacion">
        <div class="contenedor filament-page">
            <div class="contenido filament-content">
                <div class="contenedorStandar filament-card">
                    <header class="filament-header">
                        <h1 class="filament-title">Detalle del Registro</h1>
                    </header>
                    
                    <div class="filament-section">
                        <div class="filament-section-header">
                            <h2 class="filament-section-title">Información Principal</h2>
                        </div>
                        <div class="filament-table-container">
                            <table class="tablaDetalle filament-table">
                                <tbody>
                                    <tr>
                                        <th>Nombre Persona</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['NOMBRE_PERSONA']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Documento Persona</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['DOCUMENTO_PERSONA']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Registro</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['FECHA_REGISTRO']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Segerida de Pago</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['FECHA_PAGO']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Inicio</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['FECHA_INICIO']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Fecha Fin</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['FECHA_FIN']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Saldo Asignado</th>
                                        <td><?php echo htmlspecialchars(number_format($detalleSaldo['SALDO_ASIGNADO'], 2, ',', '.')); ?></td>
                                    </tr>
                                    <tr>
                                        <th>CDP</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['Numero_Documento_CDP']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>CRP</th>
                                        <td><?php echo htmlspecialchars($detalleSaldo['Numero_Documento_CRP']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Imagen Visto Bueno</th>
                                        <td>
                                            <?php if (!empty($imagenes)): ?>
                                                <?php foreach ($imagenes as $index => $imagen): ?>
                                                    <div style="margin-bottom: 1em;">
                                                        <?php 
                                                        $rutaImagen = BASE_URL . ($imagen['RUTA_IMAGEN'] ?? '');
                                                        ?>
                                                        <img src="<?php echo htmlspecialchars($rutaImagen); ?>" 
                                                            alt="Imagen asociada" 
                                                            class="imagen-ampliable" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#modalImagen<?php echo $index; ?>">
                                                        <small>Subida el <?php echo htmlspecialchars($imagen['FECHA_SUBIDA'] ?? 'Fecha no disponible'); ?></small>

                                                        <!-- Modal -->
                                                        <div class="modal fade" id="modalImagen<?php echo $index; ?>" tabindex="-1" aria-labelledby="modalLabel<?php echo $index; ?>" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title" id="modalLabel<?php echo $index; ?>">Imagen Ampliada</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                    </div>
                                                                    <div class="modal-body text-center">
                                                                        <img src="<?php echo htmlspecialchars($rutaImagen); ?>" 
                                                                            alt="Imagen asociada ampliada" 
                                                                            style="max-width: 100%; border: 1px solid #ccc; padding: 5px;">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p>No hay imágenes asociadas a este saldo.</p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
    
                    <div class="filament-section">
                        <div class="filament-section-header">
                            <h2 class="filament-section-title">Información del CDP Asociado</h2>
                        </div>
                        <div class="filament-table-container">
                            <table class="tablaDetalle filament-table">
                                <tbody>
                                    <?php if ($detalleCDP): ?>
                                        <?php foreach ($detalleCDP as $campo => $valor): ?>
                                            <tr>
                                                <th><?php echo htmlspecialchars($campo); ?></th>
                                                <td><?php echo $valor !== null ? htmlspecialchars($valor) : 'Información no Proporcionada'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="filament-empty">No se encontró información del CDP.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
    
                    <div class="filament-section">
                        <div class="filament-section-header">
                            <h2 class="filament-section-title">Información del CRP Asociado</h2>
                        </div>
                        <div class="filament-table-container">
                            <table class="tablaDetalle filament-table">
                                <tbody>
                                    <?php if ($detalleCRP): ?>
                                        <?php foreach ($detalleCRP as $campo => $valor): ?>
                                            <tr>
                                                <th><?php echo htmlspecialchars($campo); ?></th>
                                                <td><?php echo $valor !== null ? htmlspecialchars($valor) : 'Información no Proporcionada'; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="2" class="filament-empty">No se encontró información del CRP.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once $_SERVER['DOCUMENT_ROOT'] . '/public/share/footer.php'; ?>
</body>
</html>