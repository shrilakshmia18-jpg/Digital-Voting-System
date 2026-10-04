<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Withdraw</title>
<style>
body {
  font-family: Arial;
  background: url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp?x26372') no-repeat center center fixed;
  background-size: cover;
  margin: 0;
  padding: 30px;
}
.container {
  max-width: 900px;
  margin: auto;
  background: rgba(255,255,255,0.95);
  padding: 20px;
  border-radius: 10px;
}
table { width: 100%; border-collapse: collapse; }
th, td { padding: 8px; border: 1px solid #ccc; text-align: left; }
th { background: #d63031; color: #fff; }
.btn {
  padding: 6px 10px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  background: #d63031;
  color: #fff;
}
</style>
</head>
<body>
<div class="container">
<h2>Withdraw Candidates</h2>

<?php
// ✅ Handle Withdraw click
if (isset($_GET['withdraw'])) {
    $id = (int) $_GET['withdraw'];

    // Fetch candidate details
    $stmt = $conn->prepare("SELECT * FROM nomination WHERE id = ?");
    $stmt->execute([$id]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($r) {
        // Check if already withdrawn
        $check = $conn->prepare("SELECT * FROM withdrawn WHERE nomination_id = ?");
        $check->execute([$id]);
        if ($check->fetch()) {
            echo "<script>alert('⚠️ Candidate already withdrawn');window.location='withdraw.php';</script>";
            exit;
        }

        // Insert into withdrawn table
        $stmt2 = $conn->prepare("INSERT INTO withdrawn (nomination_id, name, aadhaar, withdrawn_at) VALUES (?, ?, ?, NOW())");
        $stmt2->execute([$r['id'], $r['name'], $r['aadhaar']]);

        echo "<script>alert('✅ Candidate Withdrawn Successfully');window.location='withdraw.php';</script>";
        exit;
    } else {
        echo "<script>alert('❌ Candidate not found');</script>";
    }
}
?>

<!-- ✅ Show Only Not Withdrawn Candidates -->
<table>
<tr><th>ID</th><th>Name</th><th>Aadhaar</th><th>Action</th></tr>
<?php
// Show candidates not yet withdrawn
$sql = "
SELECT n.*
FROM nomination n
LEFT JOIN withdrawn w ON n.id = w.nomination_id
WHERE w.nomination_id IS NULL
ORDER BY n.id DESC
";
$stmt = $conn->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
    foreach ($rows as $r) {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($r['id']).'</td>';
        echo '<td>'.htmlspecialchars($r['name']).'</td>';
        echo '<td>'.htmlspecialchars($r['aadhaar']).'</td>';
        echo '<td><a href="withdraw.php?withdraw='.intval($r['id']).'"><button class="btn">Withdraw</button></a></td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="4">No active candidates found.</td></tr>';
}
?>
</table>

<!-- ✅ Withdrawn Records -->
<h3 style="margin-top:20px">Withdrawn Records</h3>
<table>
<tr><th>ID</th><th>Nomination ID</th><th>Name</th><th>Aadhaar</th><th>When</th></tr>
<?php
$stmt2 = $conn->query("SELECT * FROM withdrawn ORDER BY id DESC");
$withdraws = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if ($withdraws) {
    foreach ($withdraws as $w) {
        echo '<tr>';
        echo '<td>'.htmlspecialchars($w['id']).'</td>';
        echo '<td>'.htmlspecialchars($w['nomination_id']).'</td>';
        echo '<td>'.htmlspecialchars($w['name']).'</td>';
        echo '<td>'.htmlspecialchars($w['aadhaar']).'</td>';
        echo '<td>'.htmlspecialchars($w['withdrawn_at']).'</td>';
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="5">No withdrawn records yet.</td></tr>';
}
?>
</table>

</div>
</body>
</html>
