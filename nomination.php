<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Nomination</title>
<style>
body { font-family:Arial; background:url('https://dymk4s89vutua.cloudfront.net/wp-content/uploads/2024/07/online_voting.webp?x26372') no-repeat center center fixed; background-size:cover; margin:0; padding:30px; }
.container { max-width:900px; margin:auto; background:rgba(255,255,255,0.95); padding:25px; border-radius:12px; }
label{display:block;margin-top:10px;font-weight:bold;}
input,textarea,button{width:100%;padding:8px;margin-top:6px;border-radius:6px;border:1px solid #ccc;}
button{background:#0984e3;color:#fff;padding:10px;border:none;border-radius:6px;cursor:pointer;}
table{width:100%;border-collapse:collapse;margin-top:20px;}
th,td{padding:8px;border:1px solid #ccc;text-align:left;}
img{width:60px;height:60px;object-fit:cover;border-radius:6px;}
</style>
</head>
<body>
<div class="container">
<h2>Nomination Form</h2>
<form method="post" enctype="multipart/form-data">
  <label>Name</label><input type="text" name="name" required>
  <label>DOB</label><input type="date" name="dob" required>
  <label>Father's Name</label><input type="text" name="father" required>
  <label>Mother's Name</label><input type="text" name="mother" required>
  <label>Address</label><textarea name="address" required></textarea>
  <label>Phone</label><input type="tel" name="phone" required>
  <label>Aadhaar</label><input type="text" name="aadhaar" required>
  <label>Photo</label><input type="file" name="photo" accept="image/*" required>
  <button type="submit" name="submit">Submit</button>
</form>

<?php
if(isset($_POST['submit'])) {
  $name = $conn->real_escape_string($_POST['name']);
  $dob = $_POST['dob'];
  $father = $conn->real_escape_string($_POST['father']);
  $mother = $conn->real_escape_string($_POST['mother']);
  $address = $conn->real_escape_string($_POST['address']);
  $phone = $conn->real_escape_string($_POST['phone']);
  $aadhaar = $conn->real_escape_string($_POST['aadhaar']);

  // handle photo
  if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
    $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','webp','gif'];
    if(in_array($ext, $allowed)) {
      $filename = time().'_'.preg_replace('/[^A-Za-z0-9._-]/','_', $_FILES['photo']['name']);
      $target = 'uploads/'.$filename;
      if(move_uploaded_file($_FILES['photo']['tmp_name'], $target)) {
        $stmt = $conn->prepare("INSERT INTO nomination (name,dob,father,mother,address,phone,aadhaar,photo) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->bind_param('ssssssss',$name,$dob,$father,$mother,$address,$phone,$aadhaar,$filename);
        if($stmt->execute()){
          echo "<script>alert('Nomination saved');window.location='nomination.php';</script>";
        } else {
          echo "<p style='color:red;'>DB error: ".htmlspecialchars($conn->error)."</p>";
        }
        $stmt->close();
      } else {
        echo "<p style='color:red;'>Failed uploading photo.</p>";
      }
    } else {
      echo "<p style='color:red;'>Invalid file type.</p>";
    }
  } else {
    echo "<p style='color:red;'>Please select photo.</p>";
  }
}
?>

<h3>Saved Nominations</h3>
<table>
<tr><th>ID</th><th>Name</th><th>DOB</th><th>Phone</th><th>Aadhaar</th><th>Photo</th></tr>
<?php
$res = $conn->query("SELECT * FROM nomination ORDER BY id DESC");
if($res && $res->num_rows>0){
  while($r=$res->fetch_assoc()){
    echo '<tr>';
    echo '<td>'.htmlspecialchars($r['id']).'</td>';
    echo '<td>'.htmlspecialchars($r['name']).'</td>';
    echo '<td>'.htmlspecialchars($r['dob']).'</td>';
    echo '<td>'.htmlspecialchars($r['phone']).'</td>';
    echo '<td>'.htmlspecialchars($r['aadhaar']).'</td>';
    echo '<td><img src="uploads/'.rawurlencode($r['photo']).'" alt=""></td>';
    echo '</tr>';
  }
} else {
  echo '<tr><td colspan="6">No records.</td></tr>';
}
?>
</table>
</div>
</body>
</html>