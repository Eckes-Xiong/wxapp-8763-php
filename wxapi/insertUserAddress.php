<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);

if($json["id"]==""){
  //判断是否0条地址
  $count = $c->model->getsqlOne('SELECT COUNT(1) FROM wxapp_user_address WHERE userOpenid="'.$_SESSION['openid'].'"');
  if($count["COUNT(1)"]==0){
    $json['isMain']="1";

    //刷新常驻任务
    $resident = $c->model->getsqlOne('SELECT resident FROM wxapp_task_user where openid="'.$_SESSION["openid"].'"');
    $od = explode(",", $resident["resident"]);
    $od[0] = 100;
    $c->model->exec('UPDATE wxapp_task_user SET resident="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');

  }else{
    if($count["COUNT(1)"]==2){
      //添加成就：3条地址
      $c->model->setAchieveUser(11);
    }
    if($json['isMain']=="1"){
      $c->model->exec("UPDATE wxapp_user_address SET isMain='0' WHERE userOpenid='".$_SESSION['openid']."'");
    }
  }

  $sql = "INSERT INTO wxapp_user_address (
    name, address, phone, isMain,
    userOpenid
    ) VALUES (
    '".$json['name']."', '".$json['address']."',
    '".$json['phone']."', '".$json['isMain']."',
    '".$_SESSION['openid']."'
    )";
  $c->handleInsert($sql);
}else{

  //判断是否只有1条地址
  $count = $c->model->getsqlOne('SELECT COUNT(1) FROM wxapp_user_address WHERE userOpenid="'.$_SESSION['openid'].'"');

  if($count["COUNT(1)"]==1){
    $json['isMain']="1";
  }else{
    if($json['isMain']=="1"){
      $c->model->exec("UPDATE wxapp_user_address SET isMain='0' WHERE id!='".$json['id']."' AND userOpenid='".$_SESSION['openid']."'");
    }
  }


  $sql = "UPDATE wxapp_user_address SET name='".$json['name']."', address='".$json['address']."', phone='".$json['phone']."', isMain='".$json['isMain']."' WHERE id='".$json['id']."'";
  $c->handleUpdate($sql);
}