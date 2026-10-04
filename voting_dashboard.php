<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

// Create session table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS voting_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_name VARCHAR(255) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vname = trim($_POST['voting_name']);

    if ($vname == "") {
        $message = "Voting name cannot be empty!";
    } else {

        // Check if session exists (PDO)
        $stmt = $conn->prepare("SELECT * FROM voting_sessions WHERE session_name = ?");
        $stmt->execute([$vname]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {

            // NEW SESSION
            $insert = $conn->prepare("INSERT INTO voting_sessions (session_name) VALUES (?)");
            $insert->execute([$vname]);

            $_SESSION['session_id'] = $conn->lastInsertId();
            $_SESSION['session_name'] = $vname;

        } else {

            // OLD SESSION
            $_SESSION['session_id'] = $row['id'];
            $_SESSION['session_name'] = $row['session_name'];
        }

        // Redirect to Admin Dashboard
        header("Location: admin_dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Voting Dashboard</title>
<style>
body { background:#111; color:white; font-family:Arial; text-align:center; padding-top:120px; }
.box { background:#222; padding:20px; width:400px; margin:auto; border-radius:10px; }
input { padding:10px; width:80%; border-radius:5px; border:none; margin-top:10px; }
button { padding:10px 20px; margin-top:15px; border:none; border-radius:5px; background:#0984e3; color:white; cursor:pointer; }
button:hover { background:#1B9CFC; }
</style>
</head>
<body>

<div class="box">
<h2>🗳 Voting Dashboard</h2>

<?php if ($message): ?>
  <p style="color:red;"><?php echo $message; ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="voting_name" placeholder="Enter Voting Name" required>
    <br>
    <button type="submit">Start</button>
</form>

</div>

</body>
</html>
