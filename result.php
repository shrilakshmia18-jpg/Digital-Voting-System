<?php
include 'db_connect.php';

// Fetch candidates with total votes
$query = "
    SELECT 
        d.id, 
        d.name, 
        d.symbol, 
        d.symbol_image,
        IFNULL(SUM(cv.votes),0) AS total_votes
    FROM distribution d
    LEFT JOIN counting_votes cv ON d.id = cv.candidate_id
    GROUP BY d.id, d.name, d.symbol, d.symbol_image
    ORDER BY total_votes DESC
";
$stmt = $conn->query($query);
$candidates = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Identify winner
$winner = $candidates[0] ?? null;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>🏆 Election Result</title>
<style>
body {
  font-family: Arial;
  background: url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp') no-repeat center center fixed;
  background-size: cover;
  margin: 0; padding: 20px;
}
.container {
  width: 85%; margin: auto;
  background: rgba(255,255,255,0.95);
  border-radius: 10px; padding: 25px;
}
h2{text-align:center;color:#2d3436;}
h3{text-align:center;color:green;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ccc;padding:8px;text-align:center;}
img{width:60px;border-radius:5px;}
.winner{
  background:#55efc4;
  font-weight:bold;
}
.btn-back{
  display:inline-block;
  background:#0984e3;
  color:white;
  padding:8px 14px;
  border-radius:6px;
  text-decoration:none;
  margin-bottom:10px;
}
</style>
</head>
<body>
<div class="container">
<h2>🏆 Final Result</h2>
<a class="btn-back" href="counting.php">⬅ Back to Counting</a>

<?php if($winner): ?>
<h3>🎉 Winner: <?php echo htmlspecialchars($winner['name']); ?> (<?php echo htmlspecialchars($winner['symbol']); ?>)</h3>
<?php endif; ?>

<table>
<tr>
<th>Rank</th>
<th>Photo</th>
<th>Name</th>
<th>Symbol</th>
<th>Total Votes</th>
</tr>

<?php
$rank = 1;
foreach ($candidates as $c) {
    $rowClass = ($rank == 1) ? "winner" : "";
    echo "<tr class='$rowClass'>";
    echo "<td>$rank</td>";
    echo "<td><img src='symbols/".htmlspecialchars($c['symbol_image'])."'></td>";
    echo "<td>".htmlspecialchars($c['name'])."</td>";
    echo "<td>".htmlspecialchars($c['symbol'])."</td>";
    echo "<td><b>".(int)$c['total_votes']."</b></td>";
    echo "</tr>";
    $rank++;
}
?>
</table>
</div>
</body>
</html>
