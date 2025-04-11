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
    <title>Gráfica de Presupuesto</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/presupuestoTotal.css">
</head>
<body>
  <div class="contenedorPresupuestoTotal">
    <div class="graficaContenedor">
      <canvas id="presupuestoChart"></canvas>
    </div>

    <?php
    $presupuesto = new PresupuestoTotal();
    $datos = $presupuesto->obtenerDatosPresupuesto();
    ?>

    <div class="resultados-container">
      <div class="resultado-item valor-total">
        <div class="resultado-titulo">Valor Total</div>
        <div class="resultado-valor">$<?php echo $datos['valor_actual']; ?>
          <span class="resultado-porcentaje">100%</span>
        </div>
      </div>
        
      <div class="resultado-item saldo-disponible">
        <div class="resultado-titulo">Saldo Disponible</div>
        <div class="resultado-valor">
          $<?php echo $datos['saldo_disponible']; ?>
          <span class="resultado-porcentaje"><?php echo $datos['porcentaje_disponible']; ?>%</span>
        </div>
      </div>
        
      <div class="resultado-item consumo-cdp">
        <div class="resultado-titulo">Consumo CDP</div>
        <div class="resultado-valor">
          $<?php echo $datos['consumo_cdp']; ?>
          <span class="resultado-porcentaje"><?php echo $datos['porcentaje_consumido']; ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      Chart.register(ChartDataLabels);
      const ctx = document.getElementById('presupuestoChart').getContext('2d');
      
      new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ['Presupuesto Consumido', 'Presupuesto Disponible'],
          datasets: [{
            data: [
              <?php echo $datos['porcentaje_consumido']; ?>,
              <?php echo $datos['porcentaje_disponible']; ?>
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.8)',   // Verde para disponible
              'rgba(54, 162, 235, 0.8)'
            ],
            borderColor: [
              'rgba(255, 99, 132, 0.8)', 
              'rgba(54, 162, 235, 0.8)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: {
            duration: 1000,
            easing: 'easeOutQuart'
          },
          plugins: {
            datalabels: {
              color: '#fff',
              font: {
                weight: 'bold',
                size: 14
              },
              
            },
            legend: {
              position: 'top',
              labels: {
                font: {
                  size: 13
                },
                padding: 15
              }
            },
            title: {
              display: true,
              text: 'Distribución del Presupuesto',
              font: {
                size: 16,
                weight: 'bold'
              },
              padding: {
                top: 10,
                bottom: 15
              }
            }
          },
          layout: {
            padding: 10
          }
        }
      });
    });
  </script>
</body>
</html>