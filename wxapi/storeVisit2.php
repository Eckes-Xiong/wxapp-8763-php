<?php
require '../admin_entry.php';
//$c->model->checkWxToken(getallheaders());
//点击二级分类
$c = new WxDictController();
$s = $c->model->getSqlOne('SELECT openid FROM wxapp_store WHERE id='.$_GET['sid']);


$p = $c->model->getSqlAll("SELECT distinct typecode FROM wxapp_product_list WHERE openid='{$s["openid"]}' AND typecode like '{$_GET['pid']}%'");
$length = count($p);

$cache_3 = array();
for ($i=0; $i < $length; $i++) { 
  $arr = explode(",",$p[$i]["typecode"]);
  if(!in_array($arr[1],$cache_3)){
    array_push($cache_3, $arr[1]);
  }
}

// 3级分类
$d3 = implode(",",$cache_3);
$type_child_child = $c->model->getSqlAll("SELECT dictcode as id, dictname as name FROM wxapp_dict WHERE dictcode in({$d3}) AND parentcode='{$_GET['pid']}'");

// 3级分类 产品
$prod= $c->model->getSqlAll('SELECT id,name,cover,typedesc,subtitle,price,discount,color,size,content FROM wxapp_product_list WHERE typecode like "%'.$type_child_child[0]["id"].'" AND openid="'.$s["openid"].'" ORDER BY sort DESC');

$c->model->getJsonData(1,'success',array(
  "t3"=>$type_child_child,
  "prod"=>$prod
));