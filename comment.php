<?php

require_once "config.php";

$json_data = file_get_contents('php://input');

// Decode the JSON data into an associative array
$data = json_decode($json_data, true);

// Check if the JSON decoding was successful
if ($data === null) {
    // Handle JSON decoding error
    echo "Error decoding JSON data.";
} else {
    
    $uid = $_SESSION['uid']; 
    // Retrieve data from the JSON
    $event_id = $data['event_id'];
    $rating = $data['rating'];
    $timestamp = date("Y-m-d H:i:s"); // Current timestamp

    // Insert comment into the comment table
    $query = "INSERT INTO comment (uid, event_id, rating, timestamp) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $uid, $event_id, $rating, $timestamp);
    if ($stmt->execute()) {
        echo "Comment stored successfully.";
    } else {
        echo "Failed to store comment.";
    }
    $stmt->close();
}

$conn->close();
?>
