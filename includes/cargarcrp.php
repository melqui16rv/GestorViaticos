<?php
function processCSV($filePath, $conn) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";"); // Saltar la primera línea (cabeceras)
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $sql = "INSERT INTO crp_data VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisisssssddddssssssssssssiiiiissss", 
                $data[0], $data[1], intval($data[2]), date('Y-m-d H:i:s', strtotime($data[3])), date('Y-m-d H:i:s', strtotime($data[4])), 
                $data[5], intval($data[6]), $data[7], $data[8], $data[9], 
                floatval(limpiarValorNumerico($data[10])), floatval(limpiarValorNumerico($data[11])), 
                floatval(limpiarValorNumerico($data[12])), floatval(limpiarValorNumerico($data[13])), 
                $data[14], $data[15], $data[16], $data[17], $data[18], $data[19], 
                $data[20], $data[21], $data[22], intval($data[23]), intval($data[24]), 
                intval($data[25]), intval($data[26]), intval($data[27]), $data[28], 
                floatval(limpiarValorNumerico($data[29])), date('Y-m-d H:i:s', strtotime($data[30])), $data[31], 
                $data[32], $data[33]
            );
            $stmt->execute();
        }
        fclose($handle);
    }
}

function limpiarValorNumerico($valor) {
    // Reemplazar coma por punto para decimales
    $valor = str_replace(',', '.', $valor);
    // Eliminar todos los caracteres excepto números y puntos
    $valor = preg_replace('/[^0-9.]/', '', $valor);
    // Si hay más de un punto, eliminar los puntos adicionales
    if (substr_count($valor, '.') > 1) {
        $partes = explode('.', $valor);
        $valor = array_shift($partes) . '.' . implode('', $partes);
    }
    return $valor;
}
?>

<script>
async function uploadCSV(file) {
    const formData = new FormData();
    formData.append("file", file);

    let response = await fetch("upload.php", {
        method: "POST",
        body: formData
    });

    let result = await response.json();
    alert(result.message);
}
</script>