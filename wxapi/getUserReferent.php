<?php
require '../admin_entry.php';

$c = new WxDictController();$c->model->checkWxToken(getallheaders());
// 设置推荐人
if($_GET["rf"]){
  $id = explode("CVT00",$_GET["rf"])[1];
  $vip = $c->model->getSqlOne("SELECT isVip,openid FROM wxapp_user WHERE id=".$id);

  if($vip["isVip"]==1){
    $use = $c->model->getSqlOne("SELECT id FROM wxapp_user WHERE openid='{$_SESSION['openid']}'");
    if($use["id"] == $id){
      $c->model->getJsonData(0,'无法绑定自己~');
      exit;
    }
    $c->model->exec("UPDATE wxapp_user SET referentNum=referentNum+1 WHERE id=".$id);
    $c->model->exec("UPDATE wxapp_user SET referentId='{$_GET["rf"]}' WHERE openid='{$_SESSION['openid']}'");

    $_time = date("Y-m-d H:i:s", time()+24*60*60*5);
    $c->model->exec("INSERT INTO wxapp_user_coupon (cid, openid, endTime) VALUES ('3', '".$_SESSION['openid']."', '".$_time."')");
    //推荐人
    $c->model->exec("UPDATE wxapp_user SET integralNum=integralNum+188 WHERE id='{$id}'");
    $c->model->writeIntegralLog("+188","推荐赠礼",$vip["openid"]);
    $c->model->getJsonData(1,'绑定成功~');
    exit;
  }else{
    $c->model->getJsonData(0,'该推荐人暂未受到满月祝福，无法绑定~');
  }
  exit;
}
//获取用户推荐人信息
$data = $c->model->getSqlOne('SELECT referentId FROM wxapp_user WHERE openid="'.$_SESSION['openid'].'"');
$c->model->getJsonData(1,'success',$data);