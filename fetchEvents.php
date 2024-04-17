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
        echo "<tr>";
        echo "<td>" . $row["event_name"]. "</td>";
        echo "<td>" . $row["time"]. "</td>";
        echo "<td>" . $row["description"]. "</td>";
        // You can add additional columns if needed
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='4'>No events found</td></tr>";
}

// Close the connection
$conn->close();
?>
