<?php
require '../admin_entry.php';
//$c->model->checkWxToken(getallheaders());
//获取首页数据
$c = new WxDictController();
$sql = "SELECT * FROM wxapp_product_list WHERE id='{$_GET['id']}'";
$data = $c->model->getsqlOne($sql);

$data_ = $c->model->getsqlOne("SELECT id FROM wxapp_store WHERE openid='{$data['openid']}'");

$user = $c->model->getsqlOne('SELECT daily FROM wxapp_task_user WHERE openid="'.$_SESSION["openid"].'"');
if($user){
  $od = explode(",",$user["daily"]);
  if($od[2]<100){
    $od[2] = 100;
    $c->model->exec('UPDATE wxapp_task_user SET daily="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');
  }
}

$c->model->getJsonData(1,'success',array(
  "sid" => $data_["id"],
  "color" => $data["color"],
  "name" => $data["name"],
  "cover" => $data["cover"],
  "price" => $data["price"],
  "discount" => $data["discount"],
  "typedesc" => $data["typedesc"],
  "size" => $data["size"],
  "subtitle" => $data["subtitle"],
  "content" => $data["content"],
  "tags" => $data["tags"],
  "sales" => $data["sales"],
  "code" => $data["code"]
));
