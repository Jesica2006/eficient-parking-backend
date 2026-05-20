<?php

/* =========================
   Configuración inicial
========================= */
error_reporting(0);
ini_set('display_errors', 0);

header("Content-Type: application/json");

include "conexion.php";

/* =========================
   Obtener datos del request
========================= */
$input = file_get_contents("php://input");
$data = json_decode($input, true);

/* ===== Variables ===== */
$nombre         = $data["nombre"] ?? "";
$cedula         = $data["cedula"] ?? "";
$clave          = $data["clave"] ?? "";
$rol            = $data["rol"] ?? "";
$placa          = $data["placa"] ?? "";
$tipoVehiculo   = $data["tipoVehiculo"] ?? "";
$aceptaTerminos = $data["aceptaTerminos"] ?? 0;
$CLAVE_ADMIN = "admin123";

/* =========================
   Validación de datos
========================= */
if ($nombre == "" || $cedula == "" || $clave == "") {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

/* ===== Validación términos ===== */
if ($aceptaTerminos != 1) {
    echo json_encode([
        "success" => false,
        "message" => "Debe aceptar términos y condiciones"
    ]);
    exit;
}

if ($rol === "admin" || $rol === "seguridad") {
    if (!isset($data['claveAdmin']) || $data['claveAdmin'] !== $CLAVE_ADMIN) {
        echo json_encode([
            "success" => false,
            "message" => "Clave de administrador incorrecta"
        ]);
        exit;
    }
}
/* =========================
   Insertar en base de datos
========================= */
$sql = "INSERT INTO usuarios(
            nombre,
            cedula,
            password,
            rol,
            placa,
            tipoVehiculo,
            aceptaTerminos
        ) VALUES (
            '$nombre',
            '$cedula',
            '$clave',
            '$rol',
            '$placa',
            '$tipoVehiculo',
            '$aceptaTerminos'
        )";

/* =========================
   Ejecutar consulta
========================= */
if ($conn->query($sql)) {
    echo json_encode([
        "success" => true,
        "message" => "Usuario registrado"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Error al guardar: " . $conn->error
    ]);
}

/* =========================
   Cerrar conexión
========================= */
$conn->close();

?>