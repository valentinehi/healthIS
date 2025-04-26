<?php
include 'db.php';
header('Content-Type: application/json');

$result = $conn->query("SELECT id, name FROM programs");
$programs = [];

while ($row = $result->fetch_assoc()) {
    $programs[] = $row;
}

echo json_encode($programs);
?>
