<?php
require '../admin_entry.php';

//微信支付 支付成功
$c = new LoginController();$c->model->checkWxToken(getallheaders());
$json =json_decode(file_get_contents("php://input"),true);

$c->isWxPaySuccess($json['out_trade_no']);
//ddzt 到店自提 直接完成，status=102
$status__ = $json["addrType"]=="ddzt"?"102":"101";
$c->model->exec("UPDATE wxapp_order_cart_cache SET status='{$status__}' WHERE status=1 AND out_trade_no='{$json['out_trade_no']}'");

$this->model->exec("UPDATE wxapp_order SET status={$status__} WHERE id in({$json["oids"]})");
//支付成功，更新每日任务，每周任务，常驻任务---------------------------------
$task = $c->model->refreshDailyTask(true);
$t_daily = explode(",", $task["daily"]);
$t_weekly = explode(",", $task["weekly"]);
if($t_daily[1]<100){
  $t_daily[1]=100;
}
if($json["_amount"]>=4800 && $t_weekly[4]<100){
  $t_weekly[4]=100;
}
if($json["_amount"]==1660){
  $c->model->setAchieveUser(5);
}
if($t_weekly[1]<=100){
  $t_weekly[1]=$t_weekly[1]+25;
}
$t_resident_txt = $task["resident"];
if($json["_amount"]>=8800){
  $t_resident = explode(",", $task["resident"]);
  if($t_resident[2]<100){
    $t_resident[2]=100;
    $t_resident_txt = implode(",",$t_resident);
  }
}
$t_daily_txt = implode(",",$t_daily);
$t_weekly_txt = implode(",",$t_weekly);
$c->model->updateUserTask("daily='{$t_daily_txt}', weekly='{$t_weekly_txt}', resident='{$t_resident_txt}'");

//添加log，更新积分，更新经验值
$integral = intval($json["_amount"]/100)*2;
$_integral=0;
$c->model->writeIntegralLog("+{$integral}","购物返积分");
if($json['integral']){
  $_integral=$json['integral'];
  $c->model->writeIntegralLog("-{$_integral}","购物使用");
}
$c->model->writePayLog("-{$json['amount']}", "购物支付");
$c->model->exec("UPDATE wxapp_user SET expNum=expNum+{$integral},orderNum=orderNum+1,integralNum=integralNum-{$_integral}+{$integral} WHERE openid='{$_SESSION['openid']}'");

//更新优惠券
if($json["couponId"]){
  $c->model->exec("UPDATE wxapp_user_coupon SET status=0 WHERE id='{$json['couponId']}'");

  $task = $c->model->getsqlOne('SELECT resident FROM wxapp_task_user where openid="'.$_SESSION["openid"].'"');
  $od = explode(",",$user["resident"]);
  if($od[2]<100){
    $od[2] = $od[2]+20;
    $c->model->exec('UPDATE wxapp_task_user SET resident="'.(implode(",",$od)).'" WHERE openid="'.$_SESSION['openid'].'"');
  }
}
//------------------------------------------------------------------

//更新商户余额_amount
$c->model->exec("UPDATE wxapp_store SET scope=scope+{$json['_amount']} WHERE id='{$json['sid']}'");

$c->model->getJsonData(1,'success');