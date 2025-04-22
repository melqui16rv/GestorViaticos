<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

require_once __DIR__ . '/../../sql/conexion.php';


class graficas extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Diccionario de dependencias
    private $dependencias = [
        '09' => 'SST',
        '11' => 'Victimas',
        '20' => 'Administrativo',
        '14' => 'ENI',
        '18' => 'FIC',
        '24' => 'Construciones',
        '27' => 'Actualización',
        '28' => 'ECCL',
        '34' => 'Produccion Centros',
        '38' => 'Campesena',
        '42' => 'Bienestar Aprendiz',
        '44' => 'Apoyos Sostenible',
        '45' => 'Regular',
        '69' => 'Tecnoparque',
        '70' => 'Tecnoacademia',
        '84' => 'ECCL Campesena',
        '85' => 'Full Popular',
        '86' => 'ECCL Full Pop.',
        '90' => 'Economía Campesena y Popular'
    ];

    // Método para traducir el código de dependencia
    public function traducirDependencia($dependencia) {
        // Extraer los últimos dos dígitos (o más si aplica) del campo Dependencia
        if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($dependencia), $matches)) {
            $codigo = $matches[1];
            if (isset($this->dependencias[$codigo])) {
                return $this->dependencias[$codigo];
            }
        }
        return 'Otro';
    }

    // Puedes agregar aquí los métodos para obtener los datos agrupados por dependencia
    // Ejemplo de disparador para consulta (solo la estructura, la consulta real la defines después)
    public function obtenerConsumoPorDependenciaCDP() {
        // Ejemplo de consulta, ajusta el campo de valor según corresponda
        $sql = "SELECT Dependencia, SUM(Valor_Actual) as total_consumido FROM cdp GROUP BY Dependencia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Traducir dependencias
        foreach ($resultados as &$fila) {
            $fila['dependencia_traducida'] = $this->traducirDependencia($fila['Dependencia']);
        }
        return $resultados;
    }

    // Gráfica 1: CDP - Consumo por dependencia
    public function obtenerGraficaCDP() {
        $sql = "SELECT Dependencia, 
                       SUM(Valor_Actual) AS total_valor_actual, 
                       SUM(Saldo_por_Comprometer) AS total_saldo_por_comprometer
                FROM cdp
                GROUP BY Dependencia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $datos = [];
        foreach ($resultados as $fila) {
            // Extraer código de dependencia
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
            } else {
                $codigo = 'Otro';
            }
            $nombre = isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro';

            $valor_actual = floatval($fila['total_valor_actual']);
            $saldo_por_comprometer = floatval($fila['total_saldo_por_comprometer']);
            $valor_consumido = $valor_actual - $saldo_por_comprometer;

            $datos[] = [
                'codigo_dependencia' => $codigo,
                'nombre_dependencia' => $nombre,
                'valor_actual' => $valor_actual,
                'saldo_por_comprometer' => $saldo_por_comprometer,
                'valor_consumido' => $valor_consumido
            ];
        }
        return $datos;
    }

    // Gráfica 2: CRP - Utilización por dependencia
    public function obtenerGraficaCRP() {
        $sql = "SELECT Dependencia, 
                       SUM(Valor_Actual) AS total_valor_actual, 
                       SUM(Saldo_por_Utilizar) AS total_saldo_por_utilizar
                FROM crp
                GROUP BY Dependencia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $datos = [];
        foreach ($resultados as $fila) {
            // Extraer código de dependencia
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
            } else {
                $codigo = 'Otro';
            }
            $nombre = isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro';

            $valor_actual = floatval($fila['total_valor_actual']);
            $saldo_por_utilizar = floatval($fila['total_saldo_por_utilizar']);
            $saldo_utilizado = $valor_actual - $saldo_por_utilizar;

            $datos[] = [
                'codigo_dependencia' => $codigo,
                'nombre_dependencia' => $nombre,
                'valor_actual' => $valor_actual,
                'saldo_por_utilizar' => $saldo_por_utilizar,
                'saldo_utilizado' => $saldo_utilizado
            ];
        }
        return $datos;
    }

    // Gráfica 3: OP - Pagos por dependencia
    public function obtenerGraficaOP() {
        // Sumar Valor_Actual de CRP por dependencia
        $sqlCRP = "SELECT Dependencia, SUM(Valor_Actual) AS suma_crp
                   FROM crp
                   GROUP BY Dependencia";
        $stmtCRP = $this->conexion->prepare($sqlCRP);
        $stmtCRP->execute();
        $crpData = $stmtCRP->fetchAll(PDO::FETCH_ASSOC);

        // Sumar Valor_Neto de OP por dependencia
        $sqlOP = "SELECT Dependencia, SUM(Valor_Neto) AS suma_op
                  FROM op
                  GROUP BY Dependencia";
        $stmtOP = $this->conexion->prepare($sqlOP);
        $stmtOP->execute();
        $opData = $stmtOP->fetchAll(PDO::FETCH_ASSOC);

        // Indexar OP por código de dependencia
        $opPorDependencia = [];
        foreach ($opData as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
            } else {
                $codigo = 'Otro';
            }
            $opPorDependencia[$codigo] = floatval($fila['suma_op']);
        }

        $datos = [];
        foreach ($crpData as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
            } else {
                $codigo = 'Otro';
            }
            $nombre = isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro';

            $suma_crp = floatval($fila['suma_crp']);
            $suma_op = isset($opPorDependencia[$codigo]) ? $opPorDependencia[$codigo] : 0;
            $valor_restante = $suma_crp - $suma_op;

            $datos[] = [
                'codigo_dependencia' => $codigo,
                'nombre_dependencia' => $nombre,
                'suma_crp' => $suma_crp,
                'suma_op' => $suma_op,
                'valor_restante' => $valor_restante
            ];
        }
        return $datos;
    }
}