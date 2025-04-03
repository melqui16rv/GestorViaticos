<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

/**
 * Clase para la primera gráfica (estado del CDP).
 */
class Presupuesto_viaticos_2 extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function obtenerDatosPresupuestoViaticos() {
        try {
            $queryValorActual = "SELECT COALESCE(SUM(Valor_Actual), 0) as total_valor_actual, 
                                       COALESCE(SUM(Saldo_por_Comprometer), 0) as total_saldo 
                                FROM cdp 
                                WHERE Objeto LIKE '%VIATICOS%'";

            $stmt = $this->conexion->prepare($queryValorActual);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                return $this->valoresPorDefecto();
            }

            $valorActual         = floatval($resultado['total_valor_actual']);
            $saldoPorComprometer = floatval($resultado['total_saldo']);

            if ($valorActual == 0) {
                return $this->valoresPorDefecto();
            }

            // Consumo CDP = (Valor total) - (Saldo por comprometer)
            $consumoCDP = $valorActual - $saldoPorComprometer;

            // Porcentajes para la gráfica
            $porcentajeDisponible = ($saldoPorComprometer / $valorActual) * 100;
            $porcentajeConsumido  = ($consumoCDP / $valorActual) * 100;

            return [
                'valor_actual'         => number_format($valorActual, 2),
                'saldo_disponible'     => number_format($saldoPorComprometer, 2),
                'consumo_cdp'          => number_format($consumoCDP, 2),
                'porcentaje_disponible' => number_format($porcentajeDisponible, 2),
                'porcentaje_consumido'  => number_format($porcentajeConsumido, 2)
            ];
        } catch (PDOException $e) {
            return $this->valoresPorDefecto();
        }
    }

    private function valoresPorDefecto() {
        return [
            'valor_actual'         => '0.00',
            'saldo_disponible'     => '0.00',
            'consumo_cdp'          => '0.00',
            'porcentaje_disponible' => '0.00',
            'porcentaje_consumido'  => '0.00'
        ];
    }
}

/**
 * Clase para la segunda gráfica (ejecución real en OP).
 */
class Presupuesto_viaticos_consumidos extends Conexion {
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    public function obtenerDatosPresupuestoViaticosConsumidos() {
        try {
            // 1) Obtenemos valor actual y saldo por comprometer (CDP)
            $queryValorActual = "SELECT COALESCE(SUM(Valor_Actual), 0) as total_valor_actual, 
                                       COALESCE(SUM(Saldo_por_Comprometer), 0) as total_saldo 
                                FROM cdp 
                                WHERE Objeto LIKE '%VIATICOS%'";

            $stmt = $this->conexion->prepare($queryValorActual);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resultado) {
                return $this->valoresPorDefecto();
            }

            $valorActual         = floatval($resultado['total_valor_actual']);
            $saldoPorComprometer = floatval($resultado['total_saldo']);

            if ($valorActual == 0) {
                return $this->valoresPorDefecto();
            }

            // Lo comprometido en CDP (monto que ya no está 'libre')
            $consumoCDP = $valorActual - $saldoPorComprometer;

            // 2) Obtenemos lo efectivamente gastado con OP
            $queryConsumo = "SELECT COALESCE(SUM(Valor_Neto), 0) as total_consumido 
                             FROM op 
                             WHERE Objeto_del_Compromiso LIKE '%VIATICOS%'";
            
            $stmtConsumo = $this->conexion->prepare($queryConsumo);
            $stmtConsumo->execute();
            $resultadoConsumo = $stmtConsumo->fetch(PDO::FETCH_ASSOC);

            $consumoOP = floatval($resultadoConsumo['total_consumido']);

            // 3) Calculamos el saldo disponible de lo comprometido y porcentajes
            //    El "total" para la segunda gráfica es el comprometido (consumoCDP).
            $saldoDisponibleOP    = 0;
            $porcentajeConsumido  = 0;
            $porcentajeDisponible = 0;

            if ($consumoCDP > 0) {
                $saldoDisponibleOP    = $consumoCDP - $consumoOP;
                $porcentajeConsumido  = ($consumoOP / $consumoCDP) * 100;
                $porcentajeDisponible = 100 - $porcentajeConsumido;
            }

            return [
                // ESTE valor será el "total" de la segunda gráfica => 95,787,170.00 en tu caso
                'valor_actual'       => number_format($consumoCDP, 2),

                // Saldo disponible = (consumoCDP - consumoOP)
                'saldo_disponible'   => number_format($saldoDisponibleOP, 2),

                // Monto comprometido (CDP)
                'consumo_cdp'        => number_format($consumoCDP, 2),

                // Monto efectivamente gastado (OP)
                'consumo_op'         => number_format($consumoOP, 2),

                // Porcentajes de la segunda gráfica
                'porcentaje_disponible' => number_format($porcentajeDisponible, 2),
                'porcentaje_consumido'  => number_format($porcentajeConsumido, 2)
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
            'consumo_op' => '0.00',
            'porcentaje_disponible' => '0.00',
            'porcentaje_consumido' => '0.00'
        ];
    }
}

// Instanciamos la clase para la segunda gráfica
$presupuestoConsumidos = new Presupuesto_viaticos_consumidos();
$datosConsumidos = $presupuestoConsumidos->obtenerDatosPresupuestoViaticosConsumidos();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Gráfica de Presupuesto Viáticos</title>
  <!-- Librerías de Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/presupuesto/presupuestoTotal.css">
</head>
<body>
  <!-- PRIMERA SECCIÓN: Presupuesto basado en CDP -->
  <div class="contenedorPresupuestoTotal">
    <div class="graficaContenedor">
      <canvas id="presupuestoViaticosChart"></canvas>
    </div>
    <?php
      // Obtenemos los datos para la primera gráfica (CDP)
      $presupuesto = new Presupuesto_viaticos_2();
      $datos = $presupuesto->obtenerDatosPresupuestoViaticos();
    ?>
    <div class="resultados-container">
      <div class="resultado-item valor-total">
        <div class="resultado-titulo">Valor Total Viáticos</div>
        <div class="resultado-valor">
          $<?php echo $datos['valor_actual']; ?>
          <span class="resultado-porcentaje">100%</span>
        </div>
      </div>
        
      <div class="resultado-item saldo-disponible">
        <div class="resultado-titulo">Saldo Disponible Viáticos (CDP)</div>
        <div class="resultado-valor">
          $<?php echo $datos['saldo_disponible']; ?>
          <span class="resultado-porcentaje"><?php echo $datos['porcentaje_disponible']; ?>%</span>
        </div>
      </div>
        
      <div class="resultado-item consumo-cdp">
        <div class="resultado-titulo">Consumo CDP Viáticos</div>
        <div class="resultado-valor">
          $<?php echo $datos['consumo_cdp']; ?>
          <span class="resultado-porcentaje"><?php echo $datos['porcentaje_consumido']; ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- SEGUNDA SECCIÓN: Ejecución del presupuesto (OP) sobre lo comprometido en CDP -->
  <div class="contenedorPresupuestoTotal">
    <div class="graficaContenedor">
      <canvas id="presupuestoViaticosConsumidosChart"></canvas>
    </div>
    <div class="resultados-container">
      <!-- Valor total = monto comprometido (CDP) => 95,787,170.00 en tu caso -->
      <div class="resultado-item valor-total">
        <div class="resultado-titulo">Valor Total Comprometido</div>
        <div class="resultado-valor">
          $<?php echo $datosConsumidos['valor_actual']; ?>
          <span class="resultado-porcentaje">100%</span>
        </div>
      </div>
      
      <div class="resultado-item saldo-disponible">
        <div class="resultado-titulo">Saldo Disponible (Comprometido - OP)</div>
        <div class="resultado-valor">
          $<?php echo $datosConsumidos['saldo_disponible']; ?>
          <span class="resultado-porcentaje"><?php echo $datosConsumidos['porcentaje_disponible']; ?>%</span>
        </div>
      </div>
      
      <div class="resultado-item consumo-cdp">
        <div class="resultado-titulo">Consumo OP Viáticos</div>
        <div class="resultado-valor">
          $<?php echo $datosConsumidos['consumo_op']; ?>
          <span class="resultado-porcentaje"><?php echo $datosConsumidos['porcentaje_consumido']; ?>%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPTS DE LAS GRÁFICAS -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Registrar el plugin DataLabels
      Chart.register(ChartDataLabels);

      // =======================
      // PRIMER GRÁFICO (CDP)
      // =======================
      const ctx = document.getElementById('presupuestoViaticosChart').getContext('2d');
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
              'rgba(255, 99, 132, 0.8)',
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
              // Mostramos % y monto en la etiqueta
              formatter: (value, ctx) => {
                const labels = [
                  '$ <?php echo $datos['consumo_cdp']; ?>',
                  '$ <?php echo $datos['saldo_disponible']; ?>'
                ];
                return `${value}%\n${labels[ctx.dataIndex]}`;
              },
              textAlign: 'center'
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
              text: 'Distribución del Presupuesto de Viáticos (CDP)',
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

      // ================================
      // SEGUNDO GRÁFICO (OP sobre CDP)
      // ================================
      const ctxConsumidos = document.getElementById('presupuestoViaticosConsumidosChart').getContext('2d');
      new Chart(ctxConsumidos, {
        type: 'pie',
        data: {
          // Etiquetas que indiquen que es consumo OP vs saldo de lo comprometido
          labels: ['Presupuesto Consumido (OP)', 'Saldo Disponible (CDP - OP)'],
          datasets: [{
            data: [
              <?php echo $datosConsumidos['porcentaje_consumido']; ?>,
              <?php echo $datosConsumidos['porcentaje_disponible']; ?>
            ],
            backgroundColor: [
              'rgba(255, 99, 132, 0.8)',
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
              formatter: (value, ctx) => {
                // Mostramos el consumo_op y el saldo disponible (CDP - OP)
                const labels = [
                  '$ <?php echo $datosConsumidos['consumo_op']; ?>',
                  '$ <?php echo $datosConsumidos['saldo_disponible']; ?>'
                ];
                return `${value}%\n${labels[ctx.dataIndex]}`;
              },
              textAlign: 'center'
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
              text: 'Distribución del Presupuesto de Viáticos Consumidos (OP)',
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
