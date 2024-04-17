<?php
// Step 1: Connect to the database
require_once "config.php";

// Step 2: Check if the request contains the event ID
if (isset($_GET['event_id'])) {
    // Retrieve event ID from the request
    $event_id = $_GET['event_id'];
    
    // Check if user is logged in
    session_start();
    if(isset($_SESSION['uid'])) {
        // Retrieve user ID from session
        $uid = $_SESSION['uid'];

        // Step 3: Prepare and execute the SQL query to insert into the user_events table
        $stmt = $conn->prepare("INSERT INTO user_events (uid, event_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $uid, $event_id); // "ii" indicates two integer parameters
        $result = $stmt->execute();

        // Step 4: Check if the insertion was successful
        if ($result) {
            echo json_encode(array("success" => true, "message" => "Successfully joined event."));
        } else {
            echo json_encode(array("success" => false, "message" => "Error joining event: " . $conn->error));
        }

        // Step 5: Close the database connection and exit
        $stmt->close();
    } else {
        echo json_encode(array("success" => false, "message" => "Error: User not logged in."));
    }
    $conn->close();
    exit;
} else {
    // If event ID is not provided in the request, return an error message
    echo json_encode(array("success" => false, "message" => "Error: Event ID not provided."));
    exit;
}
?>
