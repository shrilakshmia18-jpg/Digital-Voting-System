<?php
session_start();
require 'db_connect.php';

$system_name = $_POST['system_name'];

// Check if system already exists
$stmt = $conn->prepare("SELECT id FROM systems WHERE name = ?");
$stmt->bind_param("s", $system_name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // OLD system name → use saved data
    $row = $result->fetch_assoc();
    $_SESSION['system_id'] = $row['id'];

} else {
    // NEW system name → create new system
    $stmt = $conn->prepare("INSERT INTO systems (name) VALUES (?)");
    $stmt->bind_param("s", $system_name);
    $stmt->execute();
    $_SESSION['system_id'] = $conn->insert_id;

    // Make dashboard FRESH (delete old data)
    $conn->query("TRUNCATE TABLE distribution");
    $conn->query("TRUNCATE TABLE verification");
    $conn->query("TRUNCATE TABLE voting");
    $conn->query("TRUNCATE TABLE result");
    $conn->query("TRUNCATE TABLE counting_votes");
    $conn->query("TRUNCATE TABLE round_summary");
}

header("Location: admin_dashboard.php");
exit();
?>
