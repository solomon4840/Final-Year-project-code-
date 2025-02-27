<?php
session_start();
require '../../php/db.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(["error" => "Unauthorized"]);
    exit();
}

$userEmail = $_SESSION['email'];

if (isset($_GET['receiver_email'])) {
    $receiverEmail = $_GET['receiver_email'];
    $query = "SELECT sender_email, message, timestamp 
              FROM messages 
              WHERE (sender_email = ? AND receiver_email = ?) 
                 OR (sender_email = ? AND receiver_email = ?) 
              ORDER BY timestamp ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $userEmail, $receiverEmail, $receiverEmail, $userEmail);
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
