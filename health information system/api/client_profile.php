<?php
include '../db.php';
header('Content-Type: application/json');

// Check if client_id is passed via query string
if (!isset($_GET['client_id'])) {
    echo json_encode(["status" => "error", "message" => "client_id is required"]);
    exit;
}

$client_id = intval($_GET['client_id']);

// Get client details
$clientQuery = $conn->prepare("SELECT id, name, age, contact FROM clients WHERE id = ?");
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
    SELECT p.id, p.name 
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

// Output full profile
echo json_encode([
    "status" => "success",
    "client" => $client,
    "enrolled_programs" => $programs
]);
