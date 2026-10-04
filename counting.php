<?php
include 'db_connect.php';
session_start();

// Create counting_votes table if not exists
$conn->query("CREATE TABLE IF NOT EXISTS counting_votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    round_name VARCHAR(10),
    candidate_id INT,
    votes INT DEFAULT 0,
    FOREIGN KEY (candidate_id) REFERENCES distribution(id)
)");

// Start Round
if (isset($_POST['start_round'])) {
    $_SESSION['current_round'] = $_POST['round_name'];
    $msg = "🟢 Counting Started for {$_SESSION['current_round']}";
}

// End Round
if (isset($_POST['end_round'])) {
    $msg = "🔴 Counting Ended for {$_SESSION['current_round']}";
    unset($_SESSION['current_round']);
}

// Submit Votes (Multiple Candidates)
if (isset($_POST['submit_votes'])) {
    $round = $_SESSION['current_round'] ?? '';
    $selected = $_POST['candidate_ids'] ?? [];

    if ($round != '' && !empty($selected)) {
        foreach ($selected as $cid) {
            $cid = (int)$cid;
            $check = $conn->prepare("SELECT * FROM counting_votes WHERE candidate_id=? AND round_name=?");
            $check->execute([$cid, $round]);
            if ($check->rowCount() > 0) {
                $conn->prepare("UPDATE counting_votes SET votes=votes+1 WHERE candidate_id=? AND round_name=?")->execute([$cid, $round]);
            } else {
                $conn->prepare("INSERT INTO counting_votes (round_name, candidate_id, votes) VALUES (?, ?, 1)")->execute([$round, $cid]);
            }
        }
        $msg = "✅ Votes submitted successfully!";
    } else {
        $msg = "⚠️ Please start a round and select at least one candidate!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>🗳️ Counting</title>
<style>
body { font-family: Arial; background: url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp') no-repeat center center fixed; background-size: cover; margin: 0; padding: 20px; }
.container { width: 90%; margin: auto; background: rgba(255,255,255,0.95); border-radius: 10px; padding: 25px; }
h2 {text-align:center;color:#2d3436;}
select,button{padding:8px;border-radius:5px;margin:5px;}
.btn-start{background:green;color:white;border:none;}
.btn-end{background:red;color:white;border:none;}
.btn-vote{background:#0984e3;color:#fff;border:none;padding:6px 10px;border-radius:6px;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ccc;padding:8px;text-align:center;}
img{width:50px;border-radius:5px;}
.round-info{background:#dfe6e9;padding:10px;border-radius:6px;margin-bottom:10px;}
</style>
</head>
<body>
<div class="container">
<h2>🗳️ Ballot Counting</h2>

<?php if(!empty($msg)) echo "<p><b>$msg</b></p>"; ?>

<form method="POST">
  <label><b>Select Round:</b></label>
  <select name="round_name" required>
    <option value="">-- Choose Round --</option>
    <?php for($i=1;$i<=10;$i++){ echo "<option value='R$i'>Round $i</option>"; } ?>
  </select>
  <button class="btn-start" name="start_round">Start Counting</button>
  <button class="btn-end" name="end_round">End Counting</button>
</form>

<?php if(isset($_SESSION['current_round'])): ?>
<div class="round-info">
  <h3>Current Round: <?php echo $_SESSION['current_round']; ?></h3>
</div>

<form method="POST">
<h3>Candidate List (Select to Vote)</h3>
<table>
<tr><th>S.No</th><th>Photo</th><th>Name</th><th>Symbol</th><th>Vote</th></tr>
<?php
$res = $conn->query("SELECT * FROM distribution ORDER BY id ASC");
$sn = 1;
while($row = $res->fetch(PDO::FETCH_ASSOC)){
    echo "<tr>";
    echo "<td>$sn</td>";
    echo "<td><img src='symbols/".htmlspecialchars($row['symbol_image'])."'></td>";
    echo "<td>".htmlspecialchars($row['name'])."</td>";
    echo "<td>".htmlspecialchars($row['symbol'])."</td>";
    echo "<td><input type='checkbox' name='candidate_ids[]' value='{$row['id']}'></td>";
    echo "</tr>";
    $sn++;
}
?>
</table>
<button type="submit" name="submit_votes" class="btn-vote">Submit Votes</button>
</form>
<?php endif; ?>
</div>
</body>
</html>
