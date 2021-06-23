<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//获取用户的购物车
$c = new LoginController();

$wxapp = $c->model->getsqlOne("SELECT id, amount, _amount, couponId, integral, out_trade_no, oids, sid FROM wxapp_order_cart_cache WHERE status=1 AND openid='{$_SESSION["openid"]}'");
if($wxapp!=false){
  // 先查询微信订单
  $url = 'https://api.mch.weixin.qq.com/pay/orderquery';
  $data = array();
  $data['appid'] = $c->appid;
  $data['mch_id'] = $c->mchid;
  $data['nonce_str'] = $c->getRanStr();
  $data['out_trade_no'] = $wxapp['out_trade_no'];//订单编号
  //$data['sign_type']= 'HMAC-SHA256';
  $signStr = "appid=".$c->appid."&mch_id=".$c->mchid."&nonce_str=".$data['nonce_str']."&out_trade_no=".$wxapp['out_trade_no']."&key=".$c->apikey;
  //$data['sign'] = strtoupper(hash_hmac("sha256",$signStr,$c->apikey));
  $data['sign'] = strtoupper(md5($signStr));

  //$data = json_encode($data);
  $data = $c->ToXml($data);
  
  $header  = array(
    'Content-Type:application/json; charset=UTF-8',
    'Accept:application/json',
    'User-Agent:'."WXPaySDK/3.0.10 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$c->mchid
  );
  $ret = $c->model->curl_post_https($url,$data,$header);
  $ret = $c->fromXml($ret);

  if($ret['trade_state']=='SUCCESS'){
    //支付成功
    // _amount：优惠前总金额，单位 分
    //  amount：优惠后实际支付金额，单位 分
    $c->model->exec("UPDATE wxapp_order_cart_cache SET status=101 WHERE id='{$wxapp['id']}'");

    //支付成功，更新每日任务，每周任务，常驻任务---------------------------------
    $task = $c->model->refreshDailyTask(true);
    $t_daily = explode(",", $task["daily"]);
    $t_weekly = explode(",", $task["weekly"]);
    if($t_daily[1]<100){
      $t_daily[1]=100;
    }
    if($wxapp["_amount"]>=4800 && $t_weekly[4]<100){
      $t_weekly[4]=100;
    }
    if($t_weekly[1]<=100){
      $t_weekly[1]=$t_weekly[1]+25;
    }
    $t_resident_txt = $task["resident"];
    if($wxapp["_amount"]>=8800){
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
    $getIntegral = intval($wxapp["_amount"]/100)*2;
    $_integral=0;
    $c->model->writeIntegralLog("+{$getIntegral}","购物返积分");
    if($wxapp['integral']){
      $_integral=$wxapp['integral'];
      $c->model->writeIntegralLog("-{$_integral}","购物使用");
    }
    $c->model->writePayLog("-{$wxapp['_amount']}", "购物支付");
    $c->model->exec("UPDATE wxapp_user SET expNum=expNum+{$getIntegral},orderNum=orderNum+1,integralNum=integralNum-{$_integral}+{$getIntegral} WHERE openid='{$_SESSION['openid']}'");

    //更新优惠券
    if($wxapp["couponId"]){
      $c->model->exec("UPDATE wxapp_user_coupon SET status=0 WHERE id='{$wxapp['couponId']}'");

      $user = $c->model->getsqlOne("SELECT resident FROM wxapp_task_user where openid='{$_SESSION["openid"]}'");
      $od = explode(",",$user["resident"]);
      if($od[2]<100){
        $od[2] = $od[2]+20;
        $odt = implode(",",$od);
        $c->model->exec("UPDATE wxapp_task_user SET resident='{$odt}' WHERE openid='{$_SESSION['openid']}'");
      }
    }
    //更新商户余额_amount
    $c->model->exec("UPDATE wxapp_store SET scope=scope+{$wxapp['_amount']} WHERE id='{$wxapp['sid']}'");
    //------------------------------------------------------------------

  }else{
    $c->model->exec("DELETE FROM wxapp_order_cart_cache WHERE id= {$wxapp['id']}");
  }

}

$integral = $c->model->getSqlOne("SELECT integralNum FROM wxapp_user WHERE openid='{$_SESSION["openid"]}'");

$data = $c->model->getSqlAll("SELECT distinct sid FROM wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION["openid"]}'");
$len = count($data);

$arr = array();
$total = 0;

for($i=0; $i<$len; $i++){
  $store = $c->model->getsqlOne("SELECT isOnShop,name,address FROM wxapp_store WHERE id='{$data[$i]["sid"]}'");
  $carts = $c->model->getSqlAll("SELECT id,pname,pnumber,pcover,createTime,color,size,remark,pprice FROM wxapp_order WHERE sid='{$data[$i]["sid"]}' AND status=0 AND buyerOpenid='{$_SESSION["openid"]}'");
  array_push($arr, array(
    "name"=>$store["name"],
    "address"=>$store["address"],
    "id"=>$data[$i]["sid"],
    "isOnShop"=>$store["isOnShop"],
    "checked"=>true,
    "count"=>count($carts),
    "order"=>$carts
  ));
  $total = $total+count($carts);
}

$c->model->getJsonData(1,$integral["integralNum"],$arr,$total);


