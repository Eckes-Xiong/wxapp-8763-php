<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$id = $_GET["id"];
$c->model->exec("UPDATE wxapp_h5game_list SET play=play+1 WHERE id='{$id}'");
$c->model->getJsonData(1,'success');