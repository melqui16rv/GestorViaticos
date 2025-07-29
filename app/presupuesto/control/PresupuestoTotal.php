<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class PresupuestoTotal extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function obtenerDatosPresupuesto() {
        try {
            $queryValorActual = "SELECT COALESCE(SUM(Valor_Actual), 0) as total_valor_actual, 
                                       COALESCE(SUM(Saldo_por_Comprometer), 0) as total_saldo 
                                FROM cdp";
            
            $stmt = $this->conexion->prepare($queryValorActual);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$resultado) {
                return $this->valoresPorDefecto();
            }
            
            $valorActual = floatval($resultado['total_valor_actual']);
            $saldoPorComprometer = floatval($resultado['total_saldo']);
            
            // Evitar división por cero
            if ($valorActual == 0) {
                return $this->valoresPorDefecto();
            }
            
            // Calcular el consumo y porcentajes
            $consumoCDP = $valorActual - $saldoPorComprometer;
            $porcentajeDisponible = ($saldoPorComprometer / $valorActual) * 100;
            $porcentajeConsumido = ($consumoCDP / $valorActual) * 100;
            
            return [
                'valor_actual' => number_format($valorActual, 2),
                'saldo_disponible' => number_format($saldoPorComprometer, 2),
                'consumo_cdp' => number_format($consumoCDP, 2),
                'porcentaje_disponible' => number_format($porcentajeDisponible, 2),
                'porcentaje_consumido' => number_format($porcentajeConsumido, 2)
            ];
        } catch (PDOException $e) {
            return $this->valoresPorDefecto();
        }
    }
    
    private function valoresPorDefecto() {
        return [
            'valor_actual' => '0.00',
            'saldo_disponible' => '0.00',
            'consumo_cdp' => '0.00',
            'porcentaje_disponible' => '0.00',
            'porcentaje_consumido' => '0.00'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfica de Presupuesto Compacta</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/presupuestoTotal.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <div class="contenedorPresupuestoTotal dashboard-mini">
    <?php
    $presupuesto = new PresupuestoTotal();
    $datos = $presupuesto->obtenerDatosPresupuesto();
    ?>

    <!-- Compact Stats Display -->
    <div class="compact-stats">
      <div class="stat-compact disponible">
        <div class="stat-icon">
          <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-content">
          <div class="stat-label">Disponible</div>
          <div class="stat-value">$<?php echo $datos['saldo_disponible']; ?></div>
          <div class="stat-percent"><?php echo $datos['porcentaje_disponible']; ?>%</div>
        </div>
      </div>
      
      <div class="stat-compact consumido">
        <div class="stat-icon">
          <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
          <div class="stat-label">Consumido</div>
          <div class="stat-value">$<?php echo $datos['consumo_cdp']; ?></div>
          <div class="stat-percent"><?php echo $datos['porcentaje_consumido']; ?>%</div>
        </div>
      </div>
    </div>    <!-- Mini Progress Bar -->
    <div class="progress-container">
      <div class="progress-label">
        <span>Distribución del Presupuesto</span>
        <button class="expand-chart-btn" onclick="expandChartCompact()">
          <i class="fas fa-expand-alt"></i>
        </button>
      </div>
      <div class="progress-bar">
        <div class="progress-fill consumido" style="width: <?php echo $datos['porcentaje_consumido']; ?>%"></div>
        <div class="progress-fill disponible" style="width: <?php echo $datos['porcentaje_disponible']; ?>%"></div>
      </div>
      <div class="progress-legend">
        <div class="legend-item">
          <span class="legend-color consumido"></span>
          <span>Consumido <?php echo $datos['porcentaje_consumido']; ?>%</span>
        </div>
        <div class="legend-item">
          <span class="legend-color disponible"></span>
          <span>Disponible <?php echo $datos['porcentaje_disponible']; ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Datos del presupuesto para pasar al modal global
    const presupuestoData = {
      valor_actual: '<?php echo $datos['valor_actual']; ?>',
      saldo_disponible: '<?php echo $datos['saldo_disponible']; ?>',
      consumo_cdp: '<?php echo $datos['consumo_cdp']; ?>',
      porcentaje_disponible: '<?php echo $datos['porcentaje_disponible']; ?>',
      porcentaje_consumido: '<?php echo $datos['porcentaje_consumido']; ?>'
    };

    // Función para expandir usando el modal global
    function expandChartCompact() {
      if (typeof window.expandChartGlobal === 'function') {
        window.expandChartGlobal(presupuestoData);
      } else {
        console.error('Función expandChartGlobal no encontrada');
      }
    }
  </script>
</body>
</html>