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
    $comment_id = $data['comment_id'];
    $new_text = $data['new_text'];
    $new_rating = $data['new_rating'];

    // Check if the comment belongs to the user
    $query = "SELECT uid FROM comments WHERE comment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->bind_result($comment_uid);
    $stmt->fetch();
    $stmt->close();

    // Verify if the comment belongs to the user
    if ($uid == $comment_uid) {
        // Update the comment in the comment table
        $query = "UPDATE comments SET text = ?, rating = ? WHERE comment_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sii", $new_text, $new_rating, $comment_id);
        if ($stmt->execute()) {
            echo "Comment updated successfully.";
        } else {
            echo "Failed to update comment.";
        }
        $stmt->close();
    } else {
        echo "You do not have permission to edit this comment.";
    }
}

$conn->close();
?>
