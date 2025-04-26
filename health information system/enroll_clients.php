<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'];
$program_id = $data['program_id'];

$stmt = $conn->prepare("INSERT INTO enrollments (client_id, program_id) VALUES (?, ?)");
$stmt->bind_param("ii", $client_id, $program_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}
?>
