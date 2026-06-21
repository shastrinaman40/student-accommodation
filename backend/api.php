<?php
header('Content-Type: application/json');
require_once __DIR__ . '/connection.php';

$action = $_REQUEST['action'] ?? 'list';

function json_die($data){
    echo json_encode($data);
    exit;
}

if ($action === 'list') {
    $city = $_GET['city'] ?? '';
    $max_price = $_GET['max_price'] ?? '';
    $gender = $_GET['gender'] ?? '';

    $conds = [];
    $params = [];

    if ($city !== '') { $conds[] = "city = ?"; $params[] = $city; }
    if ($max_price !== '') { $conds[] = "price <= ?"; $params[] = $max_price; }
    if ($gender !== '') { $conds[] = "gender = ?"; $params[] = $gender; }

    $sql = "SELECT id, name, city, price, gender, rating, images FROM properties";
    if (count($conds)) $sql .= ' WHERE ' . implode(' AND ', $conds);
    $stmt = $conn->prepare($sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['images'] = $r['images'] ? explode(',', $r['images']) : [];
        $rows[] = $r;
    }
    json_die(['success' => true, 'data' => $rows]);
}

if ($action === 'detail') {
    $id = intval($_GET['id'] ?? 0);
    if (!$id) json_die(['success'=>false,'error'=>'missing id']);
    $stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $prop = $res->fetch_assoc();
    if (!$prop) json_die(['success'=>false,'error'=>'not found']);
    $prop['images'] = $prop['images'] ? explode(',', $prop['images']) : [];
    // amenities
    $stmt = $conn->prepare("SELECT a.id, a.name FROM amenities a JOIN property_amenities pa ON a.id=pa.amenity_id WHERE pa.property_id=?");
    $stmt->bind_param('i',$id);
    $stmt->execute();
    $amenR = $stmt->get_result();
    $amen = [];
    while ($a = $amenR->fetch_assoc()) $amen[] = $a;
    $prop['amenities'] = $amen;
    json_die(['success'=>true,'data'=>$prop]);
}

if ($action === 'shortlist') {
    $user_id = intval($_GET['user_id'] ?? 0);
    if (!$user_id) json_die(['success'=>false,'error'=>'missing user_id']);
    $stmt = $conn->prepare("SELECT p.* FROM properties p JOIN interested_users iu ON p.id=iu.property_id WHERE iu.user_id=?");
    $stmt->bind_param('i',$user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['images'] = $r['images'] ? explode(',', $r['images']) : [];
        $rows[] = $r;
    }
    json_die(['success'=>true,'data'=>$rows]);
}

if ($action === 'toggle_interest') {
    // expects POST: user_id, property_id
    $user_id = intval($_POST['user_id'] ?? 0);
    $property_id = intval($_POST['property_id'] ?? 0);
    if (!$user_id || !$property_id) json_die(['success'=>false,'error'=>'missing params']);
    // check existing
    $stmt = $conn->prepare("SELECT 1 FROM interested_users WHERE user_id=? AND property_id=?");
    $stmt->bind_param('ii',$user_id,$property_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->fetch_assoc()) {
        $del = $conn->prepare("DELETE FROM interested_users WHERE user_id=? AND property_id=?");
        $del->bind_param('ii',$user_id,$property_id);
        $del->execute();
        json_die(['success'=>true,'interested'=>false]);
    } else {
        $ins = $conn->prepare("INSERT INTO interested_users (user_id, property_id) VALUES (?,?)");
        $ins->bind_param('ii',$user_id,$property_id);
        $ins->execute();
        json_die(['success'=>true,'interested'=>true]);
    }
}

if ($action === 'signup') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$name || !$email || !$password) json_die(['success'=>false,'error'=>'missing fields']);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name,email,password) VALUES (?,?,?)");
    $stmt->bind_param('sss',$name,$email,$hash);
    if ($stmt->execute()) json_die(['success'=>true,'user_id'=>$stmt->insert_id]);
    json_die(['success'=>false,'error'=>'db error']);
}

if ($action === 'login') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!$email || !$password) json_die(['success'=>false,'error'=>'missing']);
    $stmt = $conn->prepare("SELECT id, password, name FROM users WHERE email=?");
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $res = $stmt->get_result();
    $u = $res->fetch_assoc();
    if ($u && password_verify($password, $u['password'])) {
        // In production use sessions / JWT. Here we return user id for demo.
        json_die(['success'=>true,'user_id'=>intval($u['id']),'name'=>$u['name']]);
    }
    json_die(['success'=>false,'error'=>'invalid']);
}

json_die(['success'=>false,'error'=>'unknown action']);
?>