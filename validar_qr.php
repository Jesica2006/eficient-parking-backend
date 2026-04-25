<?php
header("Content-Type: application/json");
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Método no permitido"
    ]);
    exit;
}

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$qr = $data["qr"] ?? "";

if (empty($qr)) {
    echo json_encode([
        "success" => false,
        "message" => "QR vacío"
    ]);
    exit;
}

// FORMATO: USER-NOMBRE-CEDULA-ESPACIO-5
$parts = explode("-", $qr);

if (count($parts) < 5 || $parts[0] !== "USER" || $parts[3] !== "ESPACIO") {
    echo json_encode([
        "success" => false,
        "message" => "QR inválido"
    ]);
    exit;
}

$cedula = $parts[2];
$espacio = $parts[4];

// 🔥 VALIDACIÓN EN BD
$stmt = $conn->prepare("
    SELECT e.*, u.nombre
    FROM espacios e
    INNER JOIN usuarios u ON e.cedula = u.cedula
    WHERE e.numero = ?
    AND e.cedula = ?
    AND e.estado = 'ocupado'
");

$stmt->bind_param("is", $espacio, $cedula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "message" => "No autorizado para este espacio"
    ]);
    exit;
}

$row = $result->fetch_assoc();

// 🔥 VALIDAR TIEMPO
if (!empty($row["tiempoLimite"])) {
    $segundos = strtotime($row["tiempoLimite"]) - time();

    if ($segundos <= 0) {
        echo json_encode([
            "success" => false,
            "message" => "Tiempo expirado"
        ]);
        exit;
    }
}

echo json_encode([
    "success" => true,
    "usuario" => $row["nombre"],
    "espacio" => $row["numero"]
]);

$stmt->close();
$conn->close();
?>