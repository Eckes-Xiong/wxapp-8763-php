<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取用户的全部收货地址
$c = new WxDictController();
$sql = 'SELECT id,address,name,phone,isMain FROM wxapp_user_address WHERE userOpenid="'.$_SESSION['openid'].'" ORDER BY isMain DESC';
$c->handleSearchAll($sql);