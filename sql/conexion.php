<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
class Conexion {
    private $host = 'localhost';
    private $dbname = 'appscide_cfpi';  
    private $user = 'appscide_Administrador';    
    private $password = 'E8oUxamqQTwtM8MrKf#LrqtxJ3p'; 
    private $port = 3306;
    private $charset = 'utf8mb4';
    private $conexion;

    public function __construct() {
        try {
            $dsn = "mysql:host=$this->host;port=$this->port;dbname=$this->dbname;charset=$this->charset";
            $this->conexion = new PDO($dsn, $this->user, $this->password);
            $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Establecer la collation
            $this->conexion->exec("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        } catch (PDOException $e) {
            echo 'Error de conexi贸n: ' . $e->getMessage();
        }
    }

    public function obtenerConexion() {
        return $this->conexion;
    }
}
?>
