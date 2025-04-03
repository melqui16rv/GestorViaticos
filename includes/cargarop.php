<?php
function processCSV($filePath, $conn) {
    if (($handle = fopen($filePath, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";"); // Saltar la primera línea (cabeceras)
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $sql = "INSERT INTO op_data VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssissddddssssssssisssddddiiiiissss", 
                $data[0], $data[1], $data[2], intval($data[3]), date('Y-m-d H:i:s', strtotime($data[4])), 
                $data[5], floatval(str_replace(["$", ","], "", $data[6])), floatval(str_replace(["$", ","], "", $data[7])), 
                floatval(str_replace(["$", ","], "", $data[8])), $data[9], $data[10], $data[11], $data[12], $data[13], 
                $data[14], $data[15], $data[16], $data[17], intval($data[18]), $data[19], $data[20], 
                $data[21], floatval($data[22]), floatval($data[23]), floatval($data[24]), 
                $data[25], intval($data[26]), intval($data[27]), intval($data[28]), intval($data[29]), 
                intval($data[30]), intval($data[31]), $data[32], intval($data[33]), $data[34], 
                $data[35], $data[36]
            );
            $stmt->execute();
        }
        fclose($handle);
    }
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
