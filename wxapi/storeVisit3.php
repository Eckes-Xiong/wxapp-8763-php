<?php
require '../admin_entry.php';
//$c->model->checkWxToken(getallheaders());
//点击3级分类
$c = new WxDictController();
$s = $c->model->getSqlOne('SELECT openid FROM wxapp_store WHERE id='.$_GET['sid']);
// 3级分类 产品
$prod= $c->model->getSqlAll('SELECT id,name,cover,typedesc,subtitle,price,discount,color,size,content FROM wxapp_product_list WHERE typecode like "%'.$_GET["pid"].'" AND openid="'.$s["openid"].'" ORDER BY sort DESC');

$c->model->getJsonData(1,'success',$prod);