<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

require_once __DIR__ . '/../../sql/conexion.php';


class graficas_general_sennova extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Solo dependencias permitidas
    private $dependencias_permitidas = ['62', '66', '69', '70'];

    // Diccionario de dependencias solo con las permitidas
    private $dependencias = [
        '62' => 'WORLDSKILLS',
        '66' => 'Semilleros de Investigación',
        '69' => 'Tecnoparque',
        '70' => 'Tecnoacademia'
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

        $filtrados = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (in_array($codigo, $this->dependencias_permitidas)) {
                    $fila['dependencia_traducida'] = $this->traducirDependencia($fila['Dependencia']);
                    $filtrados[] = $fila;
                }
            }
        }
        return $filtrados;
    }

    // Gráfica 1: CDP - Consumo por dependencia (agrupando por código)
    public function obtenerGraficaCDP() {
        $sql = "SELECT Dependencia, Valor_Actual, Saldo_por_Comprometer FROM cdp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupados = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->dependencias[$codigo],
                    'valor_actual' => 0,
                    'saldo_por_comprometer' => 0
                ];
            }
            $agrupados[$codigo]['valor_actual'] += floatval($fila['Valor_Actual']);
            $agrupados[$codigo]['saldo_por_comprometer'] += floatval($fila['Saldo_por_Comprometer']);
        }

        $datos = [];
        foreach ($agrupados as $codigo => $info) {
            $valor_consumido = $info['valor_actual'] - $info['saldo_por_comprometer'];
            $datos[] = [
                'codigo_dependencia' => $codigo,
                'nombre_dependencia' => $info['nombre_dependencia'],
                'valor_actual' => $info['valor_actual'],
                'saldo_por_comprometer' => $info['saldo_por_comprometer'],
                'valor_consumido' => $valor_consumido
            ];
        }
        return $datos;
    }

    // Gráfica 2: CRP - Utilización por dependencia (agrupando por código)
    public function obtenerGraficaCRP() {
        $sql = "SELECT Dependencia, Valor_Actual, Saldo_por_Utilizar FROM crp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupados = [];
        $otrosDebug = []; // Para depuración

        foreach ($resultados as $fila) {
            $dependencia = trim($fila['Dependencia']);
            if ($dependencia === '' || $dependencia === null) {
                continue; // Ignorar dependencias vacías o nulas
            }
            if (preg_match('/(\d{1,2}(\.\d)?$)/', $dependencia, $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->dependencias[$codigo],
                    'valor_actual' => 0,
                    'saldo_por_utilizar' => 0
                ];
            }
            $agrupados[$codigo]['valor_actual'] += is_numeric($fila['Valor_Actual']) ? floatval($fila['Valor_Actual']) : 0;
            $agrupados[$codigo]['saldo_por_utilizar'] += is_numeric($fila['Saldo_por_Utilizar']) ? floatval($fila['Saldo_por_Utilizar']) : 0;
        }

        // Si quieres depurar, puedes descomentar la siguiente línea temporalmente:
        file_put_contents('/tmp/otros_dependencias.txt', print_r($otrosDebug, true));

        $datos = [];
        foreach ($agrupados as $codigo => $info) {
            $saldo_utilizado = $info['valor_actual'] - $info['saldo_por_utilizar'];
            $datos[] = [
                'codigo_dependencia' => $codigo,
                'nombre_dependencia' => $info['nombre_dependencia'],
                'valor_actual' => $info['valor_actual'],
                'saldo_por_utilizar' => $info['saldo_por_utilizar'],
                'saldo_utilizado' => $saldo_utilizado
            ];
        }
        return $datos;
    }

    // Gráfica 3: OP - Pagos por dependencia (agrupando por código)
    public function obtenerGraficaOP() {
        // CRP
        $sqlCRP = "SELECT Dependencia, Valor_Actual FROM crp";
        $stmtCRP = $this->conexion->prepare($sqlCRP);
        $stmtCRP->execute();
        $crpData = $stmtCRP->fetchAll(PDO::FETCH_ASSOC);

        $crpAgrupados = [];
        foreach ($crpData as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($crpAgrupados[$codigo])) {
                $crpAgrupados[$codigo] = 0;
            }
            $crpAgrupados[$codigo] += is_numeric($fila['Valor_Actual']) ? floatval($fila['Valor_Actual']) : 0;
        }

        // OP
        $sqlOP = "SELECT Dependencia, Valor_Neto FROM op";
        $stmtOP = $this->conexion->prepare($sqlOP);
        $stmtOP->execute();
        $opData = $stmtOP->fetchAll(PDO::FETCH_ASSOC);

        $opAgrupados = [];
        foreach ($opData as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($opAgrupados[$codigo])) {
                $opAgrupados[$codigo] = 0;
            }
            $opAgrupados[$codigo] += is_numeric($fila['Valor_Neto']) ? floatval($fila['Valor_Neto']) : 0;
        }

        // Unir y calcular
        $codigos = array_unique(array_merge(array_keys($crpAgrupados), array_keys($opAgrupados)));
        $datos = [];
        foreach ($codigos as $codigo) {
            $suma_crp = isset($crpAgrupados[$codigo]) ? $crpAgrupados[$codigo] : 0;
            $suma_op = isset($opAgrupados[$codigo]) ? $opAgrupados[$codigo] : 0;
            $valor_restante = $suma_crp - $suma_op;
            $nombre = $this->dependencias[$codigo];
            $datos[] = [
                'codigo_dependencia' => $codigo,
                'nombre_dependencia' => $nombre,
                'suma_crp' => $suma_crp, // <-- Este es el valor correcto de CRP/RP
                'suma_op' => $suma_op,
                'valor_restante' => $valor_restante
            ];
        }
        return $datos;
    }

    // Conteo de registros por dependencia en CDP
    public function contarRegistrosPorDependenciaCDP() {
        $sql = "SELECT Dependencia FROM cdp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conteo = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->dependencias[$codigo],
                    'total' => 0
                ];
            }
            $conteo[$codigo]['total']++;
        }
        // Excluir "Otro"
        return array_values($conteo);
    }

    // Conteo de registros por dependencia en CRP
    public function contarRegistrosPorDependenciaCRP() {
        $sql = "SELECT Dependencia FROM crp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conteo = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->dependencias[$codigo],
                    'total' => 0
                ];
            }
            $conteo[$codigo]['total']++;
        }
        // Excluir "Otro"
        return array_values($conteo);
    }

    // Conteo de registros por dependencia en OP
    public function contarRegistrosPorDependenciaOP() {
        $sql = "SELECT Dependencia FROM op";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conteo = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $this->dependencias_permitidas)) continue;
            } else {
                continue;
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->dependencias[$codigo],
                    'total' => 0
                ];
            }
            $conteo[$codigo]['total']++;
        }
        // Excluir "Otro"
        return array_values($conteo);
    }

    // Totales de viáticos CDP solo para dependencias permitidas
    public function obtenerTotalesViaticosPorDependencias() {
        $dependencias = $this->dependencias_permitidas;
        $placeholders = implode(',', array_fill(0, count($dependencias), '?'));
        $sql = "SELECT SUM(Valor_Actual) as valor_actual, SUM(Saldo_por_Comprometer) as saldo_por_comprometer
                FROM cdp
                WHERE (UPPER(Objeto) LIKE '%VIATICOS%' OR UPPER(Objeto) LIKE '%VIATI%')
                  AND (";
        $sql .= implode(' OR ', array_map(function($d) { return "Dependencia LIKE ?"; }, $dependencias));
        $sql .= ")";
        $params = [];
        foreach ($dependencias as $dep) {
            $params[] = "%$dep";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'valor_actual' => floatval($row['valor_actual']),
            'saldo_por_comprometer' => floatval($row['saldo_por_comprometer'])
        ];
    }

    // Totales de viáticos OP solo para dependencias permitidas
    public function obtenerTotalesViaticosOPPorDependencias() {
        $dependencias = $this->dependencias_permitidas;
        $sql = "SELECT SUM(Valor_Neto) as valor_op
                FROM op
                WHERE (UPPER(Objeto_del_Compromiso) LIKE '%VIATICOS%' OR UPPER(Objeto_del_Compromiso) LIKE '%VIATI%')
                  AND (";
        $sql .= implode(' OR ', array_map(function($d) { return "Dependencia LIKE ?"; }, $dependencias));
        $sql .= ")";
        $params = [];
        foreach ($dependencias as $dep) {
            $params[] = "%$dep";
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'valor_op' => floatval($row['valor_op'])
        ];
    }
}