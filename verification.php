<?php
session_start();
require_once 'db_connect.php';

$msg = '';
if(isset($_POST['action']) && isset($_POST['nomination_id'])){
    $nom_id = $_POST['nomination_id'];
    $action = $_POST['action'];

    if($action === 'approve'){
        $stmt = $conn->prepare("UPDATE verification SET status='Verified', reason=NULL WHERE nomination_id=?");
        $stmt->execute([$nom_id]);
        $stmt2 = $conn->prepare("UPDATE nomination SET status='Verified' WHERE id=?");
        $stmt2->execute([$nom_id]);
        $msg = "✅ Candidate Approved";
    } elseif($action === 'reject'){
        $reason = trim($_POST['reason'] ?? '');
        if(!empty($reason)){
            $stmt = $conn->prepare("UPDATE verification SET status='Rejected', reason=? WHERE nomination_id=?");
            $stmt->execute([$reason, $nom_id]);
            $stmt2 = $conn->prepare("UPDATE nomination SET status='Rejected' WHERE id=?");
            $stmt2->execute([$nom_id]);
            $msg = "❌ Candidate Rejected";
        } else {
            $msg = "⚠️ Please enter a reason to reject!";
        }
    }
}

$pending = $conn->query("
    SELECT v.id, n.id as nomination_id, n.name, n.aadhaar, n.photo
    FROM verification v
    JOIN nomination n ON v.nomination_id=n.id
    WHERE v.status='Pending'
");

$verified = $conn->query("
    SELECT v.id, n.name, n.aadhaar, n.photo
    FROM verification v
    JOIN nomination n ON v.nomination_id=n.id
    WHERE v.status='Verified'
");

$rejected = $conn->query("
    SELECT v.id, n.name, n.aadhaar, n.photo, v.reason
    FROM verification v
    JOIN nomination n ON v.nomination_id=n.id
    WHERE v.status='Rejected'
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Verification Panel</title>
<style>
body { font-family:Arial; background:url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp') no-repeat center center fixed; background-size:cover; margin:0;padding:20px;color:#333; }
.overlay { background: rgba(255,255,255,0.95); padding:20px; max-width:900px; margin:auto; border-radius:10px; }
.card { display:flex; align-items:center; background:#eef; padding:10px; margin-bottom:8px; border-radius:6px; }
.card img { width:50px;height:50px;object-fit:cover;border-radius:4px; }
.card form { margin-left:auto; display:flex; gap:5px; align-items:center; }
input[type="submit"], input[type="text"], textarea { padding:5px 10px; border-radius:4px; cursor:pointer; }
textarea { resize:none; }
h2,h3 { margin-top:0; }
</style>
</head>
<body>
<div class="overlay">
<h2>Verification Panel</h2>
<?php if(!empty($msg)) echo "<p><b>$msg</b></p>"; ?>

<h3>Pending Candidates</h3>
<?php if($pending && $pending->rowCount()>0): ?>
    <?php while($row = $pending->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="card">
        <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="Candidate">
        <strong><?php echo htmlspecialchars($row['name']); ?></strong> &nbsp; Aadhaar: <?php echo htmlspecialchars($row['aadhaar']); ?>
        <form method="POST">
            <input type="hidden" name="nomination_id" value="<?php echo $row['nomination_id']; ?>">
            <input type="submit" name="action" value="approve">
            <textarea name="reason" placeholder="Reject Reason"></textarea>
            <input type="submit" name="action" value="reject">
        </form>
    </div>
    <?php endwhile; ?>
<?php else: ?>
<p>No pending candidates.</p>
<?php endif; ?>

<h3>✅ Verified Candidates</h3>
<?php if($verified && $verified->rowCount()>0): ?>
    <?php while($row = $verified->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="card">
        <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="Candidate">
        <strong><?php echo htmlspecialchars($row['name']); ?></strong> &nbsp; Aadhaar: <?php echo htmlspecialchars($row['aadhaar']); ?>
    </div>
    <?php endwhile; ?>
<?php else: ?>
<p>No verified candidates yet.</p>
<?php endif; ?>

<h3>❌ Rejected Candidates</h3>
<?php if($rejected && $rejected->rowCount()>0): ?>
    <?php while($row = $rejected->fetch(PDO::FETCH_ASSOC)): ?>
    <div class="card">
        <img src="uploads/<?php echo htmlspecialchars($row['photo']); ?>" alt="Candidate">
        <strong><?php echo htmlspecialchars($row['name']); ?></strong> &nbsp; Aadhaar: <?php echo htmlspecialchars($row['aadhaar']); ?>
        &nbsp; <em>Reason: <?php echo htmlspecialchars($row['reason']); ?></em>
    </div>
    <?php endwhile; ?>
<?php else: ?>
<p>No rejected candidates.</p>
<?php endif; ?>

<p><a href="nomination.php">⬅ Back to Nomination</a></p>
</div>
</body>
</html>
