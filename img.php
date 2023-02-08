<?php
    // MySQL Config

    $dbhost = 'localhost';
    $dbname = '';
    $dbusername = '';
    $dbpassword = '';
    
    // End of config

    $b64 = $_POST['b64'];
    $imgid = $_GET['id'];
    
    function generateRandomString($length = 10) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[rand(0, $charactersLength - 1)];
      }
      return $randomString;
    }

    try {
      $dbcon = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbusername, $dbpassword);
      $dbcon->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
    } catch(PDOException $e){
      die();
    }
    
    // Some hosts doesnt allow SQL Events, this means that we have to resort to some hacky solutions such as this one.
    // We clear the database everytime someone uploads an image.
    $dbcon->query("DELETE FROM images WHERE UNIX_TIMESTAMP() - created > 10");
    
    // Upload image and return the img_id.
    if (isset($b64)) {
      $ip = $_SERVER['REMOTE_ADDR'];
      $request = $dbcon->query("SELECT b64 FROM images WHERE ip = '$ip'");
      $ratelimit = $request->fetch();
      
      if ($ratelimit) {
        die();
      }
      
      $img_id = generateRandomString();
      $sql = "INSERT INTO images (img_id, b64, created, ip) VALUES (?,?,?,?)";
      $dbcon->prepare($sql)->execute([$img_id, base64_decode($b64, TRUE), time(), $ip]);
    
      echo($img_id);
    }
    
    // Return the image if the img_id exists, delete after it has been sent to the requester.
    if (isset($imgid)){
      $request = $dbcon->query("SELECT b64 FROM images WHERE img_id = '$imgid'");
      $img = $request->fetch();
    
      $request = $dbcon->prepare("DELETE FROM images WHERE img_id = '$imgid'")->execute();
      $b64data = $img['b64'];
      
      if (!$b64data) {
        die();
      }
    
      echo(base64_encode($b64data));
    }
?>
