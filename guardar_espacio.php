<?php
header("Content-Type: application/json");
include "conexion.php";

$data = json_decode(file_get_contents("php://input"), true);

$idEspacio = $data['idEspacio'] ?? null;

if (!$idEspacio) {
    echo json_encode(["success" => false, "message" => "Falta ID"]);
    exit;
}

// 🔥 USAR DateTime (NO strtotime)
$horaInicio = new DateTime("now", new DateTimeZone("America/Bogota"));

$tiempoLimite = new DateTime("now", new DateTimeZone("America/Bogota"));
$tiempoLimite->modify("+15 minutes");

$sql = "UPDATE espacios 
        SET disponible = 0,
            estado = 'ocupado',
            horaInicio = '".$horaInicio->format('Y-m-d H:i:s')."',
            tiempoLimite = '".$tiempoLimite->format('Y-m-d H:i:s')."'
        WHERE id = '$idEspacio'";

if ($conn->query($sql)) {

    echo json_encode([
        "success" => true,
        "horaInicio" => $horaInicio->format('Y-m-d H:i:s'),
        "tiempoLimite" => $tiempoLimite->format('Y-m-d H:i:s'),
        "remaining_seconds" => $tiempoLimite->getTimestamp() - $horaInicio->getTimestamp()
    ]);

} else {
    echo json_encode([
        "success" => false,
        "error" => $conn->error
    ]);
}

$conn->close();
?>