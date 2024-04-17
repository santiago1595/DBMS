<?php
// Step 1: Connect to the database
require_once "config.php";

// Step 2: Check if the request contains the RSO ID
if (isset($_GET['rso_id'])) {
    // Retrieve RSO ID from the request
    $rso_id = $_GET['rso_id'];
    
    // Check if user is logged in
    session_start();
    if(isset($_SESSION['uid'])) {
        // Retrieve user ID from session
        $uid = $_SESSION['uid'];

        // Step 3: Prepare and execute the SQL query to insert into the student_rso table
        $stmt = $conn->prepare("INSERT INTO student_rso (uid, rso_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $uid, $rso_id); // "ii" indicates two integer parameters
        $result = $stmt->execute();

        // Step 4: Check if the insertion was successful
        if ($result) {
            echo json_encode(array("success" => true, "message" => "Successfully joined RSO."));
        } else {
            echo json_encode(array("success" => false, "message" => "Error joining RSO: " . $conn->error));
        }

        // Step 5: Close the database connection and exit
        $stmt->close();
    } else {
        echo json_encode(array("success" => false, "message" => "Error: User not logged in."));
    }
    $conn->close();
    exit;
} else {
    // If RSO ID is not provided in the request, return an error message
    echo json_encode(array("success" => false, "message" => "Error: RSO ID not provided."));
    exit;
}
?>
