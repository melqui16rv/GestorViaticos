<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

class graficas_tecnoparque extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // Diccionario de dependencias
    private $dependencias = [
        '09' => 'SALUD OCUPACIONAL',
        '10' => 'DESPLAZADOS / ACCIONES REGULARES - ADMON',
        '11' => 'ARTICULACION CON LA MEDIA',
        '14' => 'ESCUELA NACIONAL DE INSTRUCTORES',
        '18' => 'FONDO INDTRIA Y DE LA CONSTR - FIC',
        '20' => 'DISENO CURRICULAR',
        '23' => 'ACTUALIZACION Y MODERNIZACION TECNOLOGICA',
        '24' => 'CONSTRUCCIONES Y ADECUACIONES',
        '27' => 'MODERNIZACION AMBIENTES',
        '28' => 'CERTIFICACION COMPETENCIAS LABORALES',
        '34' => 'PRODUCCION CENTROS',
        '38' => 'ECONOMIA CAMPESINA Y SER',
        '42' => 'BIENESTAR APRENDICES',
        '43' => 'BIENESTAR FUNCIONARIOS',
        '44' => 'APOYOS DE SOSTENIMIENTO',
        '45' => 'SERVICIOS PRESTADOS A LA FORMACION REGULAR',
        '62' => 'CAPACITACION ESPECIALIZADA WORLDSKILLS',
        '64' => 'FORMACION CONTINÚA ESPECIALIZADA',
        '65' => 'APROPIACION DE CIENCIA Y TECNOLOGIA Y CULTURA DE LA INNOVACION',
        '66' => 'INVESTIGACIÓN APLICADA Y SEMILLEROS DE INVESTIGACIÓN',
        '69' => 'TECNOPARQUE',
        '70' => 'TECNOACADEMIA',
        '84' => 'CERTIFICAC COMPETENC LABORAL - ECONOM CAMPESINA',
        '85' => 'ECONOMIA POPULAR',
        '86' => 'CERTIFICAC COMPETENC LABORAL - ECONOM POPULAR',
        '90' => 'ACCIONES REGUL- ECONOM POPUL Y CAMPESINA'
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
            } else {
                $codigo = 'Otro';
            }
            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro',
                    'valor_actual' => 0,
                    'saldo_por_comprometer' => 0
                ];
            }
            $agrupados[$codigo]['valor_actual'] += floatval($fila['Valor_Actual']);
            $agrupados[$codigo]['saldo_por_comprometer'] += floatval($fila['Saldo_por_Comprometer']);
        }

        $datos = [];
        foreach ($agrupados as $codigo => $info) {
            if ($codigo === 'Otro') continue; // Excluir "Otro"
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
            } else {
                $codigo = 'Otro';
                $otrosDebug[] = $dependencia; // Guardar para depuración
            }
            if (!isset($agrupados[$codigo])) {
                $agrupados[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro',
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
            if ($codigo === 'Otro') continue; // Excluir "Otro"
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
            } else {
                $codigo = 'Otro';
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
            } else {
                $codigo = 'Otro';
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
            if ($codigo === 'Otro') continue; // Excluir "Otro"
            $suma_crp = isset($crpAgrupados[$codigo]) ? $crpAgrupados[$codigo] : 0;
            $suma_op = isset($opAgrupados[$codigo]) ? $opAgrupados[$codigo] : 0;
            $valor_restante = $suma_crp - $suma_op;
            $nombre = isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro';
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
            } else {
                $codigo = 'Otro';
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro',
                    'total' => 0
                ];
            }
            $conteo[$codigo]['total']++;
        }
        // Excluir "Otro"
        return array_values(array_filter($conteo, function($item) {
            return $item['codigo_dependencia'] !== 'Otro';
        }));
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
            } else {
                $codigo = 'Otro';
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro',
                    'total' => 0
                ];
            }
            $conteo[$codigo]['total']++;
        }
        // Excluir "Otro"
        return array_values(array_filter($conteo, function($item) {
            return $item['codigo_dependencia'] !== 'Otro';
        }));
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
            } else {
                $codigo = 'Otro';
            }
            if (!isset($conteo[$codigo])) {
                $conteo[$codigo] = [
                    'codigo_dependencia' => $codigo,
                    'nombre_dependencia' => isset($this->dependencias[$codigo]) ? $this->dependencias[$codigo] : 'Otro',
                    'total' => 0
                ];
            }
            $conteo[$codigo]['total']++;
        }
        // Excluir "Otro"
        return array_values(array_filter($conteo, function($item) {
            return $item['codigo_dependencia'] !== 'Otro';
        }));
    }
}