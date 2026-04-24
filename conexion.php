<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "UniversidadGerardoBarrios";

$conexion = new mysqli($host, $username, $password, $database);

if ($conexion->connect_error) {
    die("Error de conexión a la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");

?>