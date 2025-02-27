<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "research_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_email = $_SESSION['email'];

$sql = "SELECT pc.id, p.name, pc.status FROM project_collaborators pc 
        JOIN projects p ON pc.project_id = p.project_id 
        WHERE pc.collaborator_email = ? AND pc.status = 'pending'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Invitations</title>
</head>
<body>
    <h2>Pending Collaboration Invitations</h2>
    <?php while ($row = $result->fetch_assoc()): ?>
        <p><?= htmlspecialchars($row['name']) ?> - 
            <a href="accept_invite.php?id=<?= $row['id'] ?>">Accept</a> | 
            <a href="reject_invite.php?id=<?= $row['id'] ?>">Reject</a>
        </p>
    <?php endwhile; ?>
</body>
</html>

<?php
$conn->close();
?>
