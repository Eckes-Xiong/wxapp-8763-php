<?php
require '../admin_entry.php';

//获取用户的全部收货地址
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$sql = 'SELECT id,address,name,phone,isMain FROM wxapp_user_address WHERE userOpenid="'.$_SESSION['openid'].'" ORDER BY isMain DESC';
$c->handleSearchAll($sql);