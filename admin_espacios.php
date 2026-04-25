<?php
header("Content-Type: application/json");
include "conexion.php";

// 🔥 LIBERAR ESPACIOS EXPIRADOS
$conn->query("
    UPDATE espacios 
    SET disponible = 1,
        estado = 'libre',
        horaInicio = NULL,
        tiempoLimite = NULL,
        cedula = NULL
    WHERE tiempoLimite IS NOT NULL 
    AND tiempoLimite <= NOW()
");

// 🔥 CONSULTA
$sql = "SELECT e.*, u.nombre 
        FROM espacios e
        LEFT JOIN usuarios u ON e.cedula = u.cedula
        ORDER BY e.zonaId ASC, e.numero ASC";

$result = $conn->query($sql);

$libres = [];
$ocupados = [];
$porVencer = [];

while ($row = $result->fetch_assoc()) {

    // 🔥 SI ESTÁ OCUPADO Y TIENE TIEMPO
    if ($row['estado'] === 'ocupado' && !empty($row['tiempoLimite'])) {

        $segundos = strtotime($row['tiempoLimite']) - time();

        $row['remaining_seconds'] = max(0, $segundos);
        $row['tiempoRestanteMin'] = max(0, ceil($segundos / 60));

        if ($segundos <= 0) {
            $libres[] = $row;
        } else if ($segundos <= 300) {
            $porVencer[] = $row;
        } else {
            $ocupados[] = $row;
        }

    } else {
        $row['remaining_seconds'] = 0;
        $row['tiempoRestanteMin'] = 0;
        $libres[] = $row;
    }
}

echo json_encode([
    "success" => true,
    "libres" => $libres,
    "ocupados" => $ocupados,
    "porVencer" => $porVencer
]);

$conn->close();
?>