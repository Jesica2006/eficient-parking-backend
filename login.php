<?php
// 🔥 CONFIGURACIÓN
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");

// 🔥 VALIDAR MÉTODO
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
    exit;
}

// 🔥 CONEXIÓN
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "eficientparkinglot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión"
    ]);
    exit;
}

// 🔥 RECIBIR DATOS
$cedula = $_POST['cedula'] ?? '';
$password = $_POST['password'] ?? '';

// 🔥 VALIDAR DATOS VACÍOS
if (empty($cedula) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan datos"
    ]);
    exit;
}

// 🔥 QUERY SEGURA (RECOMENDADO)
$stmt = $conn->prepare("SELECT * FROM usuarios WHERE cedula = ? AND password = ?");
$stmt->bind_param("ss", $cedula, $password);
$stmt->execute();

$result = $stmt->get_result();

// 🔥 RESPUESTA
if ($result && $result->num_rows > 0) {

    $user = $result->fetch_assoc();

    echo json_encode([
        "success" => true,
        "user" => [
            "cedula" => $user['cedula'],
            "nombre" => $user['nombre'],
            "placa" => $user['placa'],
            "tipoVehiculo" => $user['tipoVehiculo'],
            "rol" => trim(strtolower($user['rol'])), // 🔥 normalizado
            "zona" => $user['zona']
        ]
    ]);

} else {

    // 🔥 SIEMPRE RESPONDER (esto evita tu error)
    echo json_encode([
        "success" => false,
        "message" => "Credenciales incorrectas"
    ]);
}

// 🔥 CERRAR CONEXIÓN
$stmt->close();
$conn->close();
?>
