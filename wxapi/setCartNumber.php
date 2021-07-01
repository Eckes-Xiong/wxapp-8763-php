<?php
require '../admin_entry.php';

//设置购物车商品的数量
$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$sql_ = "UPDATE wxapp_order SET pnumber={$_GET["pnumber"]} WHERE id='{$_GET["id"]}'";
$c->model->exec($sql_);
$c->model->getJsonData(1,'修改成功');