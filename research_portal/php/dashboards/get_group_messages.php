<?php
session_start();
require '../../php/db.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

if (isset($_GET['project_id'])) {
    $projectId = $_GET['project_id'];
    $query = "SELECT sender_email, message, timestamp 
              FROM messages 
              WHERE project_id = ? 
              ORDER BY timestamp ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $projectId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    echo json_encode($messages);
    exit();
}
?>
