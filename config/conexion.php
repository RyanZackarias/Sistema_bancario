<?php
class Conexion {
    public static function conectar() {
        $db = new mysqli("localhost", "root", "", "Sistema_bancario");
        $db->set_charset("utf8mb4");
        if ($db->connect_error) {
            die("Error de conexión: " . $db->connect_error);
        }
        return $db;
    }
}