<?php
include 'db_connect.php'; // PDO connection
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Date Dashboard</title>
<style>
body{
    font-family:Arial;
    background:url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp?x26372') no-repeat center center fixed;
    background-size:cover;
    margin:0;
    padding:30px;
}
.container{
    max-width:700px;
    margin:auto;
    background:rgba(255,255,255,0.95);
    padding:20px;
    border-radius:10px;
}
input,textarea,button{
    width:100%;
    padding:8px;
    margin-top:6px;
    border-radius:6px;
    border:1px solid #ccc;
}
button{
    background:#0984e3;
    color:#fff;
    padding:10px;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:12px;
}
th,td{
    padding:8px;
    border:1px solid #ccc;
    text-align:left;
}
th{
    background:#0984e3;
    color:#fff;
}
</style>
</head>
<body>
<div class="container">
<h2>Date Dashboard</h2>

<form method="post">
  <label>Date</label>
  <input type="date" name="date" required>
  <label>Description</label>
  <textarea name="desc" rows="3" required></textarea>
  <button type="submit" name="add">Add</button>
</form>

<?php
if(isset($_POST['add'])){
    $date = $_POST['date'];
    $desc = $_POST['desc'];

    // PDO prepared statement
    $stmt = $conn->prepare("INSERT INTO date_dashboard (date, description) VALUES (:date, :desc)");
    $stmt->execute([':date'=>$date, ':desc'=>$desc]);

    echo "<script>alert('Date added');window.location='date_dashboard.php';</script>";
}
?>

<h3>Saved Dates</h3>
<table>
<tr><th>Date</th><th>Description</th></tr>
<?php
$res = $conn->query("SELECT * FROM date_dashboard ORDER BY id DESC");
$rows = $res->fetchAll(PDO::FETCH_ASSOC);

if($rows){
    foreach($rows as $d){
        echo '<tr><td>'.htmlspecialchars($d['date']).'</td><td>'.htmlspecialchars($d['description']).'</td></tr>';
    }
} else {
    echo '<tr><td colspan="2">No records.</td></tr>';
}
?>
</table>
</div>
</body>
</html>
