<?php
session_start();
include 'db_connect.php'; // your DB connection

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check credentials
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = :username AND password = :password");
    $stmt->execute([':username' => $username, ':password' => $password]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: voting_dashboard.php");
exit;

    } else {
        $message = "❌ Invalid username or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Login</title>
<style>
body {
  margin:0;
  padding:0;
  font-family: Arial;
  background: url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp') no-repeat center center fixed;
  background-size: cover;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  color: #fff;
}
.overlay {
  background: rgba(0,0,0,0.5);
  padding: 40px;
  border-radius: 10px;
  text-align: center;
}
h2 { margin-bottom: 20px; }
input[type=text], input[type=password] {
  width: 100%;
  padding: 10px;
  margin: 8px 0;
  border-radius: 6px;
  border: none;
  font-size: 15px;
}
button {
  width: 100%;
  padding: 10px;
  border:none;
  border-radius:6px;
  background:#0984e3;
  color:white;
  cursor:pointer;
  font-size:16px;
}
button:hover { background:#74b9ff; }
p { color:red; margin-top:10px; }
</style>
</head>
<body>
<div class="overlay">
  <h2>🔐 Admin Login</h2>
  <?php if ($message): ?>
    <p><?php echo $message; ?></p>
  <?php endif; ?>
  <form method="POST">
    <input type="text" name="username" placeholder="Enter Username" required>
    <input type="password" name="password" placeholder="Enter Password" required>
    <button type="submit">Login</button>
  </form>
</div>
</body>
</html>
