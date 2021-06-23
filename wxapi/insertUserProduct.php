<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
$c = new WxDictController('wxapp_product_list');
$json =json_decode(file_get_contents("php://input"),true);
$c->insertUserProduct($json);