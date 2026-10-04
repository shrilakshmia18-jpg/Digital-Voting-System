<?php
include 'db_connect.php';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>📊 Rounds Vote Summary</title>
<style>
body {
  font-family: Arial;
  background: #ecf0f1;
  margin: 0; padding: 20px;
}
.container {
  width: 90%; margin: auto;
  background: #fff;
  border-radius: 10px; padding: 25px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
h2 {text-align:center;color:#2d3436;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{border:1px solid #ccc;padding:8px;text-align:center;}
img{width:50px;border-radius:5px;}
.back-link{display:inline-block;background:#0984e3;color:#fff;text-decoration:none;padding:8px 12px;border-radius:6px;}
</style>
</head>
<body>
<div class="container">
<h2>📊 Rounds Vote Summary</h2>
<a class="back-link" href="counting.php">⬅ Back to Counting</a>

<table>
<tr>
  <th>S.No</th>
  <th>Photo</th>
  <th>Name</th>
  <th>Symbol</th>
  <th>R1</th>
  <th>R2</th>
  <th>R3</th>
  <th>R4</th>
  <th>R5</th>
  <th>R6</th>
  <th>R7</th>
  <th>R8</th>
  <th>R9</th>
  <th>R10</th>
  <th>Total</th>
</tr>
<?php
$cand = $conn->query("SELECT * FROM distribution ORDER BY id");
$sn = 1;
while($c = $cand->fetch(PDO::FETCH_ASSOC)){
  $rounds = ['R1','R2','R3','R4','R5','R6','R7','R8','R9','R10'];
  $total = 0;
  echo "<tr>";
  echo "<td>$sn</td>";
  echo "<td><img src='symbols/".htmlspecialchars($c['symbol_image'])."'></td>";
  echo "<td>".htmlspecialchars($c['name'])."</td>";
  echo "<td>".htmlspecialchars($c['symbol'])."</td>";
  foreach($rounds as $r){
    $q = $conn->prepare("SELECT votes FROM counting_votes WHERE candidate_id=? AND round_name=?");
    $q->execute([$c['id'],$r]);
    $v = $q->fetchColumn() ?: 0;
    echo "<td>$v</td>";
    $total += $v;
  }
  echo "<td><b>$total</b></td>";
  echo "</tr>";
  $sn++;
}
?>
</table>
</div>
</body>
</html>
