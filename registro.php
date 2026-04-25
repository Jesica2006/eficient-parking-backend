<?php
header("Content-Type: application/json");
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'] ?? '';
$cedula = $data['cedula'] ?? '';
$rol = $data['rol'] ?? 'student';
$claveAdmin = $data['claveAdmin'] ?? null;

// 🔥 TU CLAVE SECRETA (cámbiala)
$CLAVE_ADMIN = "ADMIN123";

// 🔥 VALIDAR ADMIN
if ($rol === 'admin') {
    if ($claveAdmin !== $CLAVE_ADMIN) {
        echo json_encode([
            "success" => false,
            "message" => "Clave de administrador incorrecta"
        ]);
        exit;
    }
}

// 🔥 INSERT
$sql = "INSERT INTO usuarios (nombre, cedula, rol)
        VALUES ('$nombre', '$cedula', '$rol')";

if ($conn->query($sql)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
}

$conn->close();
?>