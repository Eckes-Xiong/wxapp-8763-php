<?php
require '../admin_entry.php';
//$app->checkWxToken(getallheaders());
//切换店铺1级分类
$c = new WxDictController();

$s = $c->model->getSqlOne('SELECT openid FROM wxapp_store WHERE id='.$_GET['id']);
// 1级分类
$parentcode=$_GET["parentcode"];

// 2级分类
$type_child = $c->model->getSqlAll('SELECT dictcode as id, dictname as name FROM wxapp_dict WHERE parentcode="'.$parentcode.'"');
// 3级分类
$type_child_child = $c->model->getSqlAll('SELECT dictcode as id, dictname as name FROM wxapp_dict WHERE parentcode="'.$type_child[0]["id"].'"');

// 3级分类 产品
$prod= $c->model->getSqlAll('SELECT id,name,cover,typedesc,subtitle,price,discount,color,size,content FROM wxapp_product_list WHERE typecode like "%'.$type_child_child[0]["id"].'" AND openid="'.$s["openid"].'" ORDER BY sort DESC');

$c->model->getJsonData(1,'success',array(
  "t2"=>$type_child,
  "t3"=>$type_child_child,
  "prod"=>$prod
));