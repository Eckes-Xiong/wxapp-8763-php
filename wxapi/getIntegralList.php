<?php
require '../admin_entry.php';

//积分储值 list
$json =json_decode(file_get_contents("php://input"),true);
$c = new WxDictController();$c->model->checkWxToken(getallheaders());

$data = $c->model->getsqlAll("SELECT * FROM wxapp_integral ORDER BY id ASC");
$c->model->getJsonData(1,'success',$data);