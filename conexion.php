<?php
$host = getenv("MYSQLHOST");
$port = getenv("MYSQLPORT");
$user = getenv("MYSQLUSER");
$pass = getenv("MYSQLPASSWORD");
$db   = getenv("MYSQLDATABASE");

$conexion = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>