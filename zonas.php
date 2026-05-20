<?php
header("Content-Type: application/json");
include "conexion.php";

$sql = "SELECT * FROM zonas";
$result = $conn->query($sql);

$zonas = [];

while ($row = $result->fetch_assoc()) {
    $zonas[] = $row;
}

echo json_encode([
    "success" => true,
    "zonas" => $zonas
]);

$conn->close();