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
    // Assuming you have a session or authentication to get the user ID
    $uid = $_SESSION['uid']; // Assuming you have stored the user ID in the session

    // Retrieve data from the JSON
    $event_id = $data['event_id'];

    // Check if the event is public, private, or an RSO event
    $query = "SELECT event_type FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $stmt->bind_result($event_type);
    $stmt->fetch();
    $stmt->close();

    // Check if the event type allows joining
    if ($event_type == 'public' || $event_type == 'private') {
        // Insert user-event association into the junction table
        $query = "INSERT INTO user_event (uid, event_id) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $uid, $event_id);
        if ($stmt->execute()) {
            echo "User joined event successfully.";
        } else {
            echo "Failed to join event.";
        }
        $stmt->close();
    } elseif ($event_type == 'rso') {
        // Check if the user is a member of the RSO hosting the event
        $query = "SELECT rso_id FROM rso_member WHERE uid = ? AND rso_id = (SELECT rso_id FROM rso_events WHERE event_id = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $uid, $event_id);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();

        if ($num_rows > 0) {
            // Insert user-event association into the junction table
            $query = "INSERT INTO user_event (uid, event_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $uid, $event_id);
            if ($stmt->execute()) {
                echo "User joined RSO event successfully.";
            } else {
                echo "Failed to join RSO event.";
            }
            $stmt->close();
        } else {
            echo "User is not a member of the RSO hosting this event.";
        }
    } else {
        echo "Event type is invalid.";
    }
}

$conn->close();
?>
