<?php
session_start();

// Admin login check
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

// Voting session check
if (!isset($_SESSION['session_id'])) {
    header("Location: voting_dashboard.php");
    exit;
}

$session_name = $_SESSION['session_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body {
  margin:0; padding:0; font-family:Arial;
  background:url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp') no-repeat center center fixed;
  background-size:cover; color:#fff;
}
.overlay { 
  background:rgba(0,0,0,0.5); 
  min-height:100vh; 
  padding:40px; 
}
.box { 
  max-width:900px; 
  margin:auto; 
  text-align:center; 
}
h1 {
  margin:0 0 20px 0; 
  padding:20px; 
  background:rgba(0,0,0,0.6); 
  display:inline-block; 
  border-radius:8px; 
}
.session-label {
  font-size:22px;
  font-weight:bold;
  margin-bottom:20px;
  background:rgba(0,0,0,0.4);
  padding:10px 20px;
  border-radius:8px;
  display:inline-block;
}
.btn-container { 
  margin-top:30px; 
}
a.button { 
  display:inline-block; 
  margin:10px; 
  padding:14px 22px; 
  background:#0984e3; 
  color:white; 
  text-decoration:none; 
  border-radius:8px; 
}
a.button:hover { 
  background:#0652DD; 
}
.logout-btn {
  display:inline-block;
  margin-top:30px;
  padding:12px 20px;
  background:#d63031;
  color:white;
  text-decoration:none;
  border-radius:8px;
  font-weight:bold;
}
.logout-btn:hover { background:#ff7675; }
</style>
</head>
<body>
<div class="overlay">
  <div class="box">
    
    <h1>Admin Dashboard</h1>

    <!-- SHOW CURRENT VOTING NAME -->
    <div class="session-label">
      Voting Name: <?php echo htmlspecialchars($session_name); ?>
    </div>

    <div class="btn-container">
      <a class="button" href="date_dashboard.php">Date Dashboard</a>
      <a class="button" href="nomination.php">Nomination</a>
      <a class="button" href="verification.php">Verification</a>
      <a class="button" href="withdraw.php">Withdraw</a>
      <a class="button" href="distribution.php">Distribution</a>
      <a class="button" href="Counting.php">Counting</a>
      <a class="button" href="round_summary.php">Rounds Counts</a>
      <a class="button" href="Result.php">Result</a>
    </div>

    <!-- LOGOUT -->
    <a class="logout-btn" href="logout.php">Logout</a>

  </div>
</div>
</body>
</html>
