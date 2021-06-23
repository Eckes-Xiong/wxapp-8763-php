<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//审核店铺
$c = new WxDictController('wxapp_store');
$c->setStore($_GET['status'],$_GET['o']);