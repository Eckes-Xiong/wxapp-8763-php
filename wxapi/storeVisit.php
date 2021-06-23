<?php
require '../admin_entry.php';
//$app->checkWxToken(getallheaders());
//获取店铺商品列表
$c = new WxDictController();

$s = $c->model->getSqlOne('SELECT id,openid,type FROM wxapp_store WHERE id='.$_GET['id']);
// 1级分类
$type= $c->model->getSqlAll('SELECT dictcode as id, dictname as name FROM wxapp_dict WHERE dictcode in('.$s["type"].')');
$parentcode=$type[0]["id"];

$p = $c->model->getSqlAll("SELECT distinct typecode FROM wxapp_product_list WHERE openid='{$s["openid"]}'");
$length = count($p);

$cache_2 = array();
$cache_3 = array();
for ($i=0; $i < $length; $i++) { 
  $arr = explode(",",$p[$i]["typecode"]);
  if(!in_array($arr[0],$cache_2)){
    array_push($cache_2, $arr[0]);
  }
  if(!in_array($arr[1],$cache_3)){
    array_push($cache_3, $arr[1]);
  }
}
// 2级分类 parentcode="'.$parentcode.'"'
$d2 = implode(",",$cache_2);
$type_child = $c->model->getSqlAll("SELECT dictcode as id, dictname as name FROM wxapp_dict WHERE dictcode in({$d2})");


// 3级分类
$d3 = implode(",",$cache_3);
$type_child_child = $c->model->getSqlAll("SELECT dictcode as id, dictname as name FROM wxapp_dict WHERE dictcode in({$d3}) AND parentcode='{$type_child[0]['id']}'");

// 3级分类 产品
$prod= $c->model->getSqlAll('SELECT id,name,cover,typedesc,subtitle,price,discount,color,size,content FROM wxapp_product_list WHERE typecode like "%'.$type_child_child[0]["id"].'" AND openid="'.$s["openid"].'" ORDER BY sort DESC');

$c->model->getJsonData(1,'success',array(
  "t1"=>$type,
  "t2"=>$type_child,
  "t3"=>$type_child_child,
  "prod"=>$prod,
  "sid"=>$s["id"]
));