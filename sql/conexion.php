<?php
// Mostrar errores generados por alguna acción
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Conexion {
    private $host = 'localhost';
    private $dbname = 'union_prueba';  
    private $user = 'root';    
    private $password = ''; 
    private $port = 3306;
    private $charset = 'utf8mb4';
    private $conexion;
    // @Kiara03#

    public function __construct() {
        try {
            $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=$this->charset";
            $this->conexion = new PDO($dsn, $this->user, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Establecer la collation
            $this->conexion->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        } catch (PDOException $e) {
            echo 'Error de conexión: ' . $e->getMessage();
        }
    }

    public function obtenerConexion() {
        return $this->conexion;
    }
}
?>
