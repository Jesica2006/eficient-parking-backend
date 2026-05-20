<?php

/* =========================
   Configuración
========================= */
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");

/* =========================
   Validar método HTTP
========================= */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
    exit;
}

/* =========================
   Conexión a la base de datos
========================= */
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "eficientparkinglot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión"
    ]);
    exit;
}

/* =========================
   Recibir datos
========================= */
$cedula  = $_POST['cedula'] ?? '';
$password = $_POST['password'] ?? '';

/* =========================
   Validar datos
========================= */
if (empty($cedula) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan datos"
    ]);
    exit;
}

/* =========================
   Consulta segura (Prepared Statement)
========================= */
$stmt = $conn->prepare(
    "SELECT * FROM usuarios WHERE cedula = ? AND password = ?"
);

$stmt->bind_param("ss", $cedula, $password);
$stmt->execute();

$result = $stmt->get_result();

/* =========================
   Procesar respuesta
========================= */
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "user" => [
            "cedula"        => $user['cedula'],
            "nombre"        => $user['nombre'],
            "placa"         => $user['placa'],
            "tipoVehiculo"  => $user['tipoVehiculo'],
            "rol"           => trim(strtolower($user['rol'])), // normalizado
            "zona"          => $user['zona']
        ]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Credenciales incorrectas"
    ]);
}

/* =========================
   Cerrar conexión
========================= */
$stmt->close();
$conn->close();

?>