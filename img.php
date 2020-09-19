<?php
$dbhost = 'localhost';
$dbname = '';
$dbusername = '';
$dbpassword = '';

$b64 = $_POST['b64'];
$imgid = $_GET['id'];

try {
  $dbcon = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);
  $dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
} catch(PDOException $e){
  die();
}

if ($imgid && $b64 || !$imgid && !$b64) {
  $secret = $_GET['secret'];
  if (isset($secret) && $secret == '9048jnb83h548934598459845j'){
    $curtime = time();
    $request = $dbcon->query("SELECT * FROM images");
    $result = $request->fetchAll(PDO::FETCH_ASSOC);

    foreach($result as $image) {
      if ($curtime - $image['created'] > 10) {
        $loopid = $image['id'];
        $request = $dbcon->prepare("DELETE FROM images WHERE id = '$loopid'")->execute();
      }
    }
  }
  die();
}

function generateRandomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}
if (isset($b64)) {
  $ip = $_SERVER['REMOTE_ADDR'];
  $request = $dbcon->query("SELECT b64 FROM images WHERE ip = '$ip'");
  $ratelimit = $request->fetch();
  
  if ($ratelimit) {
    die();
  }
  
  $id = generateRandomString();
  $sql = "INSERT INTO images (id, b64, created, ip) VALUES (?,?,?,?)";
  $dbcon->prepare($sql)->execute([$id, base64_decode($b64, TRUE), time(), $ip]);

  echo($id);
} if (isset($imgid)){
  $request = $dbcon->query("SELECT b64 FROM images WHERE id = '$imgid'");
  $img = $request->fetch();

  $request = $dbcon->prepare("DELETE FROM images WHERE id = '$imgid'")->execute();
  $b64data = $img['b64'];
  
  if (!$b64data) {
    die();
  }

  echo(base64_encode($b64data));
}
?>
