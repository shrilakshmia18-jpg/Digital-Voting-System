<?php
session_start();
require_once 'db_connect.php';

// Ensure folders exist
$uploadsDir = __DIR__ . '/uploads';
$symbolsDir = __DIR__ . '/symbols';
if (!file_exists($uploadsDir)) mkdir($uploadsDir, 0777, true);
if (!file_exists($symbolsDir)) mkdir($symbolsDir, 0777, true);

$msg = '';
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nomination_id'])){
    $nom_id = $_POST['nomination_id'];
    $symbol = trim($_POST['symbol']);

    $symbol_image = '';
    if(!empty($_FILES['symbol_image']['name'])){
        $file_name = time().'_'.basename($_FILES['symbol_image']['name']);
        $target = $symbolsDir.'/'.$file_name;
        if(move_uploaded_file($_FILES['symbol_image']['tmp_name'],$target)){
            $symbol_image = $file_name;
        } else {
            $msg = "❌ Image upload failed!";
        }
    }

    // Prevent duplicate assign
    $check = $conn->prepare("SELECT * FROM distribution WHERE nomination_id=?");
    $check->execute([$nom_id]);
    if(!$check->fetch(PDO::FETCH_ASSOC)){
        $stmt = $conn->prepare("
            INSERT INTO distribution (nomination_id,name,aadhaar,symbol,symbol_image,assigned_at)
            SELECT id,name,aadhaar,?,?,NOW() FROM nomination WHERE id=?
        ");
        $stmt->execute([$symbol,$symbol_image,$nom_id]);
        $msg = "✅ Symbol Assigned Successfully!";
    } else {
        $msg = "⚠️ Candidate already assigned!";
    }
}

// Fetch verified candidates (not assigned yet)
$verified = $conn->query("
    SELECT n.id as nomination_id,n.name,n.aadhaar,n.photo 
    FROM nomination n 
    LEFT JOIN distribution d ON n.id=d.nomination_id 
    JOIN verification v ON n.id=v.nomination_id 
    WHERE v.status='Verified' AND d.nomination_id IS NULL
");

// Fetch assigned symbols
$assigned = $conn->query("SELECT * FROM distribution ORDER BY assigned_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Symbol Assignment Panel</title>
<style>
body{font-family:Arial; background:url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp') no-repeat center center fixed; background-size:cover; margin:0; padding:20px;}
.container{width:80%; margin:auto; background:rgba(255,255,255,0.95); padding:20px; border-radius:10px;}
.card{display:flex; align-items:center; background:#eef; padding:10px; margin-bottom:8px; border-radius:6px;}
.card img{width:50px;height:50px;border-radius:4px; object-fit:cover;}
.card form{margin-left:auto; display:flex; gap:5px; align-items:center;}
input[type="text"], input[type="file"]{padding:5px; border-radius:4px;}
.btn{padding:6px 12px; background:green; color:white; border:none; border-radius:4px; cursor:pointer;}
table{width:100%; border-collapse:collapse; margin-top:20px;}
th,td{border:1px solid #000; padding:8px; text-align:center;}
th{background:#0984e3; color:#fff;}
img.table-img{width:50px; height:50px; object-fit:cover; border-radius:4px;}
</style>
</head>
<body>
<div class="container">
<h2>Symbol Assignment Panel</h2>
<?php if(!empty($msg)) echo "<p><b>$msg</b></p>"; ?>

<h3>Verified Candidates (Not Assigned)</h3>
<?php if($verified && $verified->rowCount()>0): ?>
<?php while($row = $verified->fetch(PDO::FETCH_ASSOC)): ?>
<div class="card">
<img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="Candidate">
<strong style="margin-left:10px;"><?php echo htmlspecialchars($row['name']); ?></strong>
&nbsp; Aadhaar: <?php echo htmlspecialchars($row['aadhaar']); ?>

<form method="POST" enctype="multipart/form-data">
<input type="hidden" name="nomination_id" value="<?php echo $row['nomination_id']; ?>">
<input type="text" name="symbol" placeholder="Enter Symbol Name" required>
<input type="file" name="symbol_image" accept="image/*">
<button class="btn">Assign</button>
</form>
</div>
<?php endwhile; ?>
<?php else: ?>
<p>✅ All verified candidates already assigned.</p>
<?php endif; ?>

<h3>Assigned Symbols List</h3>
<table>
<tr><th>Name</th><th>Symbol</th><th>Symbol Image</th><th>Assigned At</th></tr>
<?php if($assigned && $assigned->rowCount()>0): ?>
<?php while($a=$assigned->fetch(PDO::FETCH_ASSOC)): ?>
<tr>
<td><?php echo htmlspecialchars($a['name']);?></td>
<td><?php echo htmlspecialchars($a['symbol']);?></td>
<td><?php if(!empty($a['symbol_image'])): ?><img class="table-img" src="symbols/<?php echo htmlspecialchars($a['symbol_image']); ?>"><?php else: ?><em>No image</em><?php endif;?></td>
<td><?php echo htmlspecialchars($a['assigned_at']);?></td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="4">No symbols assigned yet.</td></tr>
<?php endif;?>
</table>
</div>
</body>
</html>
