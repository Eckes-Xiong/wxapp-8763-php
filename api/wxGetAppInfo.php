<?php
require '../admin_entry.php';
$c = new WxController();
$res = $c->getOpenidAndSessionKey($_GET['code']);
if($res!==false){
  $c->getAccessToken();
}
echo $res;