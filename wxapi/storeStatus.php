<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//查询用户店铺状态
$c = new WxDictController('wxapp_store');
$data = $c->model->getSqlOne("SELECT id,name,cover,subtitle,scope,bg,level,type,typeName,status,statusDesc,address,isOnShop FROM wxapp_store WHERE openid='{$_SESSION['openid']}'");
$c->model->getJsonData(1,'',$data);