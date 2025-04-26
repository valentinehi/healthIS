<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$search = $data['search'];

$stmt = $conn->prepare("SELECT id, name, age, contact FROM clients WHERE name LIKE ?");
$like = "%$search%";
$stmt->bind_param("s", $like);
$stmt->execute();

$result = $stmt->get_result();
$clients = [];

while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}

echo json_encode($clients);
?>
