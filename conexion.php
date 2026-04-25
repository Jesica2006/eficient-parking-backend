<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "eficientparkinglot";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 🔥 FORZAR ZONA HORARIA
date_default_timezone_set("America/Bogota");
$conn->query("SET time_zone = '-05:00'");
?>