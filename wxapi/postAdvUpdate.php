<?php
require '../admin_entry.php';

$c = new WxDictController('wxapp_adv');$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);
$sql = "UPDATE wxapp_adv SET cover='".$json['cover']."', status='".$json['status']."', name='".$json['name']."', url='".$json['url']."', sort='".$json['sort']."' WHERE id='".$json['id']."'";
$c->handleUpdate($sql);