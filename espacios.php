<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");

$conexion = new mysqli("localhost", "root", "", "eficientparkinglot");

if ($conexion->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "Error de conexión"
    ]);
    exit;
}

// 🔥 LIBERAR ESPACIOS VENCIDOS AUTOMÁTICAMENTE
$conexion->query("
    UPDATE espacios 
    SET disponible = 1,
        estado = 'libre',
        horaInicio = NULL,
        tiempoLimite = NULL
    WHERE tiempoLimite IS NOT NULL 
    AND tiempoLimite < NOW()
");



// 🔥 RECIBIR JSON
$data = json_decode(file_get_contents("php://input"), true);

$zona = $data['zona'] ?? null;
$tipoVehiculo = $data['tipoVehiculo'] ?? null;

// 🔥 VALIDACIÓN
if (!$zona || !$tipoVehiculo) {
    echo json_encode([
        "success" => false,
        "message" => "Datos incompletos"
    ]);
    exit;
}

// 🔥 QUERY CORRECTA SEGÚN TU BD
$sql = "SELECT * FROM espacios 
        WHERE zonaId = ? 
        AND tipoVehiculo = ? 
        AND disponible = 1
        ORDER BY numero ASC";

$stmt = $conexion->prepare($sql);

// 🚨 SI FALLA LA QUERY
if (!$stmt) {
    echo json_encode([
        "success" => false,
        "error_sql" => $conexion->error
    ]);
    exit;
}

$stmt->bind_param("is", $zona, $tipoVehiculo);
$stmt->execute();

$resultado = $stmt->get_result();

$espacios = [];

while ($row = $resultado->fetch_assoc()) {
    $espacios[] = $row;
}

echo json_encode([
    "success" => true,
    "espacios" => $espacios
]);

$stmt->close();
$conexion->close();

?>