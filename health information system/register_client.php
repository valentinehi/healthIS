<?php
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];
$age = $data['age'];
$contact = $data['contact'];

$stmt = $conn->prepare("INSERT INTO clients (name, age, contact) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $name, $age, $contact);
$stmt->execute();

echo json_encode(["status" => "success"]);
?>