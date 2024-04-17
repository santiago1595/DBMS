<?php
// Step 1: Connect to the database
require_once "config.php";

// Step 2: Query the database to fetch RSOs
$sql = "SELECT * FROM rso";
$result = $conn->query($sql);

// Step 3: Retrieve the data and return it as JSON
$rsos = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $rsos[] = $row;
    }
    echo json_encode($rsos);
} else {
    echo json_encode(array()); // Return an empty array if no RSOs found
}

// Close the connection
$conn->close();
?>
