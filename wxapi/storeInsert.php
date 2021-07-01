<?php
require '../admin_entry.php';

$c = new WxDictController('wxapp_store');$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$c->insertNewStore($json);