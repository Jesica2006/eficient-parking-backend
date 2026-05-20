<?php
$host = getenv("MYSQLHOST") ?: "localhost";
$port = getenv("MYSQLPORT") ?: "3306";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "";
$db   = getenv("MYSQLDATABASE") ?: "eficientparkinglot";

$conn = new mysqli($host, $user, $pass, $db, (int)$port);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// 🔥 FORZAR ZONA HORARIA (¡MUY IMPORTANTE!)
date_default_timezone_set("America/Bogota");
$conn->query("SET time_zone = '-05:00'");
?>
