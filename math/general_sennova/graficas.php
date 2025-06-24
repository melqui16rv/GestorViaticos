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

    // Diccionario de dependencias solo con las permitidas en la vista SENNOVA
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

    // Método para obtener el filtro de dependencia según el rol
    private function getFiltroDependenciaPorRol() {
        if (!isset($_SESSION)) {
            session_start();
        }
        $rol = isset($_SESSION['id_rol']) ? $_SESSION['id_rol'] : null;
        if ($rol == '5') {
            return ['69']; // Tecnoparque
        } elseif ($rol == '6') {
            return ['70']; // Tecnoacademia
        } elseif ($rol == '4') {
            return $this->dependencias_permitidas; // General
        }
        return $this->dependencias_permitidas; // Por defecto
    }

    // Puedes agregar aquí los métodos para obtener los datos agrupados por dependencia
    // Ejemplo de disparador para consulta (solo la estructura, la consulta real la defines después)
    public function obtenerConsumoPorDependenciaCDP() {
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT Dependencia, SUM(Valor_Actual) as total_consumido FROM cdp GROUP BY Dependencia";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $filtrados = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (in_array($codigo, $filtroDependencias)) {
                    $fila['dependencia_traducida'] = $this->traducirDependencia($fila['Dependencia']);
                    $filtrados[] = $fila;
                }
            }
        }
        return $filtrados;
    }

    // Gráfica 1: CDP - Consumo por dependencia (agrupando por código)
    public function obtenerGraficaCDP() {
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT Dependencia, Valor_Actual, Saldo_por_Comprometer FROM cdp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupados = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $filtroDependencias)) continue;
            } else {
                continue;
            }
            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    'nombre_dependencia' => $this->traducirDependencia($fila['Dependencia']),
                    'codigo_dependencia' => $codigo,
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
                'nombre_dependencia' => $info['nombre_dependencia'],
                'codigo_dependencia' => $info['codigo_dependencia'],
                'valor_actual' => $info['valor_actual'],
                'saldo_por_comprometer' => $info['saldo_por_comprometer'],
                'valor_consumido' => $valor_consumido
            ];
        }
        return $datos;
    }

    // Gráfica 2: CRP - Utilización por dependencia (agrupando por código)
    public function obtenerGraficaCRP() {
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT Dependencia, Valor_Actual, Saldo_por_Utilizar FROM crp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $agrupados = [];
        foreach ($resultados as $fila) {
            $dependencia = trim($fila['Dependencia']);
            if ($dependencia === '' || $dependencia === null) {
                continue; // Ignorar dependencias vacías o nulas
            }
            if (preg_match('/(\d{1,2}(\.\d)?$)/', $dependencia, $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $filtroDependencias)) continue;
            } else {
                continue;
            }
            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    'nombre_dependencia' => $this->traducirDependencia($fila['Dependencia']),
                    'codigo_dependencia' => $codigo,
                    'valor_actual' => 0,
                    'saldo_por_utilizar' => 0
                ];
            }
            $agrupados[$codigo]['valor_actual'] += floatval($fila['Valor_Actual']);
            $agrupados[$codigo]['saldo_por_utilizar'] += floatval($fila['Saldo_por_Utilizar']);
        }

        $datos = [];
        foreach ($agrupados as $codigo => $info) {
            $saldo_utilizado = $info['valor_actual'] - $info['saldo_por_utilizar'];
            $datos[] = [
                'nombre_dependencia' => $info['nombre_dependencia'],
                'codigo_dependencia' => $info['codigo_dependencia'],
                'valor_actual' => $info['valor_actual'],
                'saldo_por_utilizar' => $info['saldo_por_utilizar'],
                'saldo_utilizado' => $saldo_utilizado
            ];
        }
        return $datos;
    }

    // Gráfica 3: OP - Pagos por dependencia (agrupando por código)
    public function obtenerGraficaOP() {
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        // CRP
        $sqlCRP = "SELECT Dependencia, Valor_Actual FROM crp";
        $stmtCRP = $this->conexion->prepare($sqlCRP);
        $stmtCRP->execute();
        $crpData = $stmtCRP->fetchAll(PDO::FETCH_ASSOC);

        $crpAgrupados = [];
        foreach ($crpData as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $filtroDependencias)) continue;
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
                if (!in_array($codigo, $filtroDependencias)) continue;
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
            $nombre = isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro';
            $datos[] = [
                'nombre_dependencia' => $nombre,
                'codigo_dependencia' => $codigo,
                'suma_crp' => $suma_crp,
                'suma_op' => $suma_op,
                'valor_restante' => $valor_restante
            ];
        }
        return $datos;
    }

    // Conteo de registros por dependencia en CDP
    public function contarRegistrosPorDependenciaCDP() {
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT Dependencia FROM cdp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conteo = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $filtroDependencias)) continue;
            } else {
                continue;
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->traducirDependencia($fila['Dependencia']),
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
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT Dependencia FROM crp";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conteo = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $filtroDependencias)) continue;
            } else {
                continue;
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->traducirDependencia($fila['Dependencia']),
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
        $filtroDependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT Dependencia FROM op";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $conteo = [];
        foreach ($resultados as $fila) {
            if (preg_match('/(\d{1,2}(\.\d)?$)/', trim($fila['Dependencia']), $matches)) {
                $codigo = $matches[1];
                if (!in_array($codigo, $filtroDependencias)) continue;
            } else {
                continue;
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => $this->traducirDependencia($fila['Dependencia']),
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
        $dependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT SUM(Valor_Actual) as valor_actual, SUM(Saldo_por_Comprometer) as saldo_por_comprometer
                FROM cdp
                WHERE (UPPER(Objeto) LIKE '%VIATICOS%' OR UPPER(Objeto) LIKE '%VIATI%' OR UPPER(Objeto) LIKE '%TRANSPO%')
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
        $dependencias = $this->getFiltroDependenciaPorRol();
        $sql = "SELECT SUM(Valor_Neto) as valor_op
                FROM op
                WHERE (UPPER(Objeto_del_Compromiso) LIKE '%VIATICOS%' OR UPPER(Objeto_del_Compromiso) LIKE '%VIATI%' OR UPPER(Objeto_del_Compromiso) LIKE '%TRANSPO%')
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