<?php
require_once __DIR__ . '/../backend/connection.php';
$id = intval($_GET['id'] ?? 0);
if (!$id) { echo 'Missing id'; exit; }
$stmt = $conn->prepare('SELECT * FROM properties WHERE id=?');
$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
$prop = $res->fetch_assoc();
if (!$prop) { echo 'Property not found'; exit; }
$images = $prop['images'] ? explode(',', $prop['images']) : [];
$amenStmt = $conn->prepare('SELECT a.name FROM amenities a JOIN property_amenities pa ON a.id=pa.amenity_id WHERE pa.property_id=?');
$amenStmt->bind_param('i',$id);
$amenStmt->execute();
$amenRes = $amenStmt->get_result();
$amenities = [];
while($a = $amenRes->fetch_assoc()) $amenities[] = $a['name'];
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlspecialchars($prop['name']); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
  <h1><?php echo htmlspecialchars($prop['name']); ?></h1>
  <p><strong>City:</strong> <?php echo htmlspecialchars($prop['city']); ?> &nbsp; <strong>Price:</strong> ₹<?php echo htmlspecialchars($prop['price']); ?></p>
  <div id="gallery" class="mb-3">
    <?php foreach($images as $img): ?>
      <img src="/<?php echo trim($img); ?>" style="max-width:200px;margin-right:8px;"/>
    <?php endforeach; ?>
  </div>
  <p><?php echo nl2br(htmlspecialchars($prop['description'])); ?></p>
  <p><strong>Amenities:</strong> <?php echo htmlspecialchars(implode(', ', $amenities)); ?></p>
  <p><strong>Rating:</strong> <?php echo htmlspecialchars($prop['rating']); ?></p>
  <button id="interest-btn" data-id="<?php echo $prop['id']; ?>" class="btn btn-outline-primary">Mark Interested</button>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#interest-btn').on('click', function(){
  const pid = $(this).data('id');
  // demo user id=1
  $.post('/backend/api.php?action=toggle_interest', {user_id:1, property_id: pid}, function(resp){
    if (resp.success) {
      $('#interest-btn').text(resp.interested ? 'Interested ✓' : 'Mark Interested');
    }
  }, 'json');
});
</script>
</body>
</html>