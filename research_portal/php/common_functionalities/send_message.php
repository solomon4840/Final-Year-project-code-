<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
session_start();
require '../../php/db.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit();
}

$senderEmail = $_SESSION['email'];
$message = $_POST['message'];
$receiverEmail = $_POST['receiver_email'] ?? null;
$projectId = $_POST['project_id'] ?? null;

if (!$message) {
    echo json_encode(["status" => "error", "message" => "Message cannot be empty"]);
    exit();
}

$query = "INSERT INTO messages (sender_email, receiver_email, project_id, message) 
          VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssis", $senderEmail, $receiverEmail, $projectId, $message);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to send message"]);
}
exit();
?>
