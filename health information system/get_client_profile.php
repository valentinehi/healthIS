<?php
include 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$client_id = $data['client_id'];

// Get client details
$clientQuery = $conn->prepare("SELECT * FROM clients WHERE id = ?");
$clientQuery->bind_param("i", $client_id);
$clientQuery->execute();
$clientResult = $clientQuery->get_result();

if ($clientResult->num_rows === 0) {
    echo json_encode(["status" => "error", "message" => "Client not found"]);
    exit;
}
$client = $clientResult->fetch_assoc();

// Get enrolled programs
$programsQuery = $conn->prepare("
    SELECT p.name 
    FROM enrollments e 
    JOIN programs p ON e.program_id = p.id 
    WHERE e.client_id = ?
");
$programsQuery->bind_param("i", $client_id);
$programsQuery->execute();
$programsResult = $programsQuery->get_result();

$programs = [];
while ($row = $programsResult->fetch_assoc()) {
    $programs[] = $row;
}

// Output client and enrolled programs
echo json_encode([
    "client" => $client,
    "programs" => $programs
]);
