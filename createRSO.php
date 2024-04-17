<?php

require_once "config.php";

// Start the session
session_start();

// Ensure JSON content type
header("Content-Type: application/json");

// Retrieve JSON data from the request body
$json_data = file_get_contents('php://input');

// Decode the JSON data into an associative array
$data = json_decode($json_data, true);

// Check if the JSON decoding was successful
if ($data === null) {
    // Handle JSON decoding error
    echo json_encode(array("error" => "Error decoding JSON data."));
} else {
    $rso_name = $data['rsoName'];
    $rso_description = $data['rsoDescription'];

    // Check if admin ID is stored in session
    if (isset($_SESSION['admin_id'])) {
        $admin_id = $_SESSION['admin_id'];

        // Insert RSO into the RSO table
        $query = "INSERT INTO rso (name, rso_description, admin_id) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $rso_name, $rso_description, $admin_id);
        if ($stmt->execute()) {
            echo json_encode(array("message" => "RSO created successfully."));
        } else {
            echo json_encode(array("error" => "Failed to create RSO."));
        }
        $stmt->close();
    } else {
        echo json_encode(array("error" => "Admin ID not found in session."));
    }
}

$conn->close();
?>
