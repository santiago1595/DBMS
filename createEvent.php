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
    $event_name = $data['event_name'];
    $event_date = $data['event_date'];
    $event_time = $data['event_time'];
    $location_name = $data['location_name'];
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];
    $description = $data['event_description'];
    $event_type = $data['event_type']; // assuming this comes from a dropdown/select input
    $admin_id = $_SESSION['admin']; // assuming you have already authenticated the admin and stored their ID in a session

    // Insert location into the location table (if it doesn't exist)
    $query = "INSERT INTO location (name, latitude, longitude) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sdd", $location_name, $latitude, $longitude);
    $stmt->execute();
    $location_id = $conn->insert_id; // Retrieve the last auto-generated ID
    $stmt->close();

    // Insert common event information into the events table
    $query = "INSERT INTO events (event_name, date, time, locid, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssis", $event_name, $event_date, $event_time, $location_id, $description);
    $stmt->execute();
    $event_id = $conn->insert_id; // Retrieve the last auto-generated ID
    $stmt->close();

    // Insert event specific information into the respective event type table
    switch ($event_type) {
        case 'Public':
            $event_table = 'public_events';
            break;
        case 'Private':
            $event_table = 'private_events';
            break;
        case 'RSO':
            $event_table = 'rso_events';
            break;
        default:
            // Handle invalid event type
            break;
    }

    $query = "INSERT INTO $event_table (event_id) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->close();

    // Insert event reference into admin's event table
    $query = "INSERT INTO admin_events (admin_id, event_id) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $admin_id, $event_id);
    $stmt->execute();
    $stmt->close();

    // Close database connection
    $conn->close();

    // Redirect to a confirmation page or perform any other actions
    header("Location: home.html");
    exit();
}


?>