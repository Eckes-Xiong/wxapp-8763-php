<?php
require '../admin_entry.php';

$c = new WxDictController('wxapp_product_list');$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$c->insertUserProduct($json);