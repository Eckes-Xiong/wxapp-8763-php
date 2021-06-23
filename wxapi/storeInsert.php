<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
$c = new WxDictController('wxapp_store');
$json =json_decode(file_get_contents("php://input"),true);
$c->insertNewStore($json);