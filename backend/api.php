<?php
header('Content-Type: application/json');
require_once __DIR__ . '/connection.php';
session_start();

$action = $_REQUEST['action'] ?? 'list';

function json_die($data){
    echo json_encode($data);
    exit;
}

if ($action === 'list') {
    $city = $_GET['city'] ?? '';
    $max_price = $_GET['max_price'] ?? '';
    $gender = $_GET['gender'] ?? '';
    $q = $_GET['q'] ?? '';
    $page = max(1, intval($_GET['page'] ?? 1));
    $per_page = max(1, intval($_GET['per_page'] ?? 6));

    $conds = [];
    $params = [];

    if ($city !== '') { $conds[] = "city = ?"; $params[] = $city; }
    if ($max_price !== '') { $conds[] = "price <= ?"; $params[] = $max_price; }
    if ($gender !== '') { $conds[] = "gender = ?"; $params[] = $gender; }
    if ($q !== '') { $conds[] = "(name LIKE ? OR description LIKE ? OR city LIKE ?)"; $params[] = "%$q%"; $params[] = "%$q%"; $params[] = "%$q%"; }

    $where = count($conds) ? (' WHERE ' . implode(' AND ', $conds)) : '';

    // total count
    $countSql = "SELECT COUNT(*) as cnt FROM properties" . $where;
    $countStmt = $conn->prepare($countSql);
    if ($params) {
        $types = str_repeat('s', count($params));
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $countRes = $countStmt->get_result()->fetch_assoc();
    $total = intval($countRes['cnt']);

    $offset = ($page - 1) * $per_page;
    $sql = "SELECT id, name, city, price, gender, rating, images FROM properties" . $where . " ORDER BY id DESC LIMIT ?,?";
    // add offset and limit to params
    $execParams = $params;
    $execParams[] = $offset;
    $execParams[] = $per_page;
    $stmt = $conn->prepare($sql);
    if ($execParams) {
        // build types: original params are strings, offset/limit are integers
        $types = str_repeat('s', count($params)) . 'ii';
        $stmt->bind_param($types, ...$execParams);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        $r['images'] = $r['images'] ? explode(',', $r['images']) : [];
        $rows[] = $r;
    }
    json_die(['success' => true, 'data' => $rows, 'total' => $total, 'page' => $page, 'per_page' => $per_page]);
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
    // include whether current session user is interested
    $is_interested = false;
    $user_id = $_SESSION['user_id'] ?? 0;
    if ($user_id) {
        $chk = $conn->prepare("SELECT 1 FROM interested_users WHERE user_id=? AND property_id=?");
        $chk->bind_param('ii', $user_id, $id);
        $chk->execute();
        $r = $chk->get_result();
        if ($r->fetch_assoc()) $is_interested = true;
    }
    $prop['is_interested'] = $is_interested;
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
    // expects POST: property_id; user from session (preferred) or user_id
    $user_id = intval($_SESSION['user_id'] ?? ($_POST['user_id'] ?? 0));
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

if ($action === 'is_interested') {
    $property_id = intval($_GET['property_id'] ?? 0);
    $user_id = intval($_SESSION['user_id'] ?? 0);
    if (!$user_id || !$property_id) json_die(['success'=>true,'interested'=>false]);
    $chk = $conn->prepare("SELECT 1 FROM interested_users WHERE user_id=? AND property_id=?");
    $chk->bind_param('ii',$user_id,$property_id);
    $chk->execute();
    $res = $chk->get_result();
    json_die(['success'=>true,'interested'=> (bool)$res->fetch_assoc() ]);
}

if ($action === 'me') {
    if (isset($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT id, name, email, phone FROM users WHERE id=?");
        $stmt->bind_param('i',$uid);
        $stmt->execute();
        $res = $stmt->get_result();
        $u = $res->fetch_assoc();
        json_die(['success'=>true,'user'=>['user_id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'phone'=>$u['phone']]]);
    }
    json_die(['success'=>false,'user'=>null]);
}

if ($action === 'profile_update') {
    $uid = $_SESSION['user_id'] ?? 0;
    if (!$uid) json_die(['success'=>false,'error'=>'not logged in']);
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    if (!$name) json_die(['success'=>false,'error'=>'missing name']);
    $stmt = $conn->prepare("UPDATE users SET name=?, phone=? WHERE id=?");
    $stmt->bind_param('ssi',$name,$phone,$uid);
    if ($stmt->execute()) json_die(['success'=>true]);
    json_die(['success'=>false,'error'=>'db']);
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    json_die(['success'=>true]);
}

if ($action === 'signup') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    if (!$name || !$email || !$password) json_die(['success'=>false,'error'=>'missing fields']);
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name,email,password,phone) VALUES (?,?,?,?)");
    $stmt->bind_param('ssss',$name,$email,$hash,$phone);
    if ($stmt->execute()) {
        $uid = $stmt->insert_id;
        // auto-login
        $_SESSION['user_id'] = $uid;
        $_SESSION['name'] = $name;
        json_die(['success'=>true,'user_id'=>$uid]);
    }
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
        $_SESSION['user_id'] = intval($u['id']);
        $_SESSION['name'] = $u['name'];
        json_die(['success'=>true,'user_id'=>intval($u['id']),'name'=>$u['name']]);
    }
    json_die(['success'=>false,'error'=>'invalid']);
}

json_die(['success'=>false,'error'=>'unknown action']);
?>