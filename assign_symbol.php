<?php
// assign_symbol.php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: distribution.php');
    exit;
}

// Required fields
$verification_id = $_POST['verification_id'] ?? null;
$symbol_file = $_POST['symbol_file'] ?? null;

if (empty($verification_id) || empty($symbol_file)) {
    die('Invalid submission. Please select a candidate and a symbol.');
}

// 1) Get candidate info from verification + nomination
$stmt = $conn->prepare("SELECT v.nomination_id, n.name, n.aadhaar FROM verification v LEFT JOIN nomination n ON v.nomination_id = n.id WHERE v.id = ?");
if (!$stmt) {
    die("DB error: " . $conn->error);
}
$stmt->bind_param('i', $verification_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows !== 1) {
    $stmt->close();
    die('Candidate not found or invalid verification id.');
}
$c = $res->fetch_assoc();
$stmt->close();

$nomination_id = $c['nomination_id'];
$candidate_name = $c['name'];
$candidate_aadhaar = $c['aadhaar'];

// Sanitize symbol file name (ensure it exists)
$symbol_file_clean = basename($symbol_file);
$symbol_path = __DIR__ . '/symbols/' . $symbol_file_clean;
if (!file_exists($symbol_path)) {
    die('Symbol image not found on server.');
}

// 2) Insert into distribution table
$stmt2 = $conn->prepare("INSERT INTO distribution (nomination_id, name, aadhaar, symbol, symbol_image, assigned_at) VALUES (?, ?, ?, ?, ?, NOW())");
if (!$stmt2) {
    die("DB prepare error: " . $conn->error);
}
$symbol_label = pathinfo($symbol_file_clean, PATHINFO_FILENAME);
$stmt2->bind_param(types: 'issss', $nomination_id, $candidate_name, $candidate_aadhaar, $symbol_label, $symbol_file_clean);
$ok = $stmt2->execute();
if (!$ok) {
    $err = $stmt2->error;
    $stmt2->close();
    die("DB execute error: " . $err);
}
$stmt2->close();

// Optional: you might want to mark verification/status so it can't be assigned twice.
// Example (uncomment if desired):
// $upd = $conn->prepare("UPDATE verification SET status = 'Assigned' WHERE id = ?");
// $upd->bind_param('i', $verification_id);
// $upd->execute();
// $upd->close();

header('Location: distribution.php?assigned=1');
exit;
