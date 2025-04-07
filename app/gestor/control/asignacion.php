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
                                </tbody>
                            </table>
                            
                            <!-- ===============
                            SECCIÓN NUEVA: MUESTRA DE IMÁGENES
                            ================ -->
                            <?php
                            // 1) Obtener todas las imágenes del saldo:
                            $imagenes = $miClaseG->obtenerImagenesDeSaldo($idSaldo);
                            ?>

                            <?php if (!empty($imagenes)): ?>
                            <div class="filament-section">
                                <div class="filament-section-header">
                                    <h2 class="filament-section-title">Imagen de Visto Bueno</h2>
                                </div>
                                <div class="filament-table-container">
                                    <?php foreach ($imagenes as $imagen): ?>
                                        <div style="margin-bottom: 1em;">

                                            <!--<p><strong>Nombre original:</strong> 
                                                <?php #echo htmlspecialchars($imagen['NOMBRE_ORIGINAL'] ?? 'Nombre no disponible'); ?>
                                            </p>-->

                                            <?php 
                                            // Concatenar BASE_URL con RUTA_IMAGEN
                                            $rutaImagen = BASE_URL . ($imagen['RUTA_IMAGEN'] ?? '');
                                            ?>
                                            <img src="<?php echo htmlspecialchars($rutaImagen); ?>" 
                                                alt="Imagen asociada" 
                                                style="max-width: 300px; display: block; border: 1px solid #ccc; padding: 5px;">
                                            <small>Subida el <?php echo htmlspecialchars($imagen['FECHA_SUBIDA'] ?? 'Fecha no disponible'); ?></small>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="filament-section">
                                <div class="filament-section-header">
                                    <h2 class="filament-section-title">Imágenes Asociadas</h2>
                                </div>
                                <p>No hay imágenes asociadas a este saldo.</p>
                            </div>
                            <?php endif; ?>
                            <!-- FIN DE SECCIÓN DE IMÁGENES -->
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