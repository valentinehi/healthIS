
<?php
include 'db.php';
header('Content-Type: application/json');

// Get the input data
$data = json_decode(file_get_contents('php://input'), true);
if (isset($data['name']) && !empty($data['name'])) {
    $name = $data['name'];

    // Insert the program into the database
    $stmt = $conn->prepare("INSERT INTO programs (name) VALUES (?)");
    $stmt->bind_param("s", $name);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Program name is required"]);
}
?>
