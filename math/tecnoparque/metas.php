<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/conf/config.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/sql/conexion.php';

require_once __DIR__ . '/../../sql/conexion.php';

class metas_tecnoparque extends Conexion{
    private $conexion;

    public function __construct() {
        $this->conexion = new Conexion();
        $this->conexion = $this->conexion->obtenerConexion();
    }

    // select
    public function obtenerProyectosTec() {

    }
    public function obtenerAsesorarAso() {

    }
    public function obtenerAsesorarApre() {

    }
    public function obtenerProyectosExt() {

    }
    public function obtenerVisitasApre() {

    }

    // uptade
    public function actualizarProyectosTec() {

    }
    public function actualizarAsesorarAso() {

    }
    public function actualizarAsesorarApre() {

    }
    public function actualizarProyectosExt() {

    }
    public function actualizarVisitasApre() {

    }

    // insert
    public function insertarProyectosTec() {

    }
    public function insertarAsesorarAso() {

    }
    public function insertarAsesorarApre() {

    }
    public function insertarProyectosExt() {

    }
    public function insertarVisitasApre() {

    }
    
    // delete
    public function eliminarProyectosTec() {

    }
    public function eliminarAsesorarAso() {

    }
    public function eliminarAsesorarApre() {

    }
    public function eliminarProyectosExt() {

    }
    public function eliminarVisitasApre() {

    }
}