<?php
// Step 1: Connect to the database
require_once "config.php";

// Step 2: Query the database
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

// Step 3: Retrieve the data
if ($result->num_rows > 0) {
    // Step 4: Display the data on the webpage
    while($row = $result->fetch_assoc()) {
        echo "Event Name: " . $row["event_name"]. " - Date: " . $row["time"]. "<br>";
    }
} else {
    echo "0 results";
}

// Close the connection
$conn->close();
?>
