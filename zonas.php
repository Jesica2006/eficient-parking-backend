<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Content-Type: application/json");
include "conexion.php";

$sql = "SELECT * FROM zonas";
$result = $conn->query($sql);

$zonas = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $zonas[] = $row;
    }
}

echo json_encode($zonas);

$conn->close();
?>