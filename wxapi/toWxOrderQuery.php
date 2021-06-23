<?php
require '../admin_entry.php';
$app->checkWxToken(getallheaders());
//微信支付 查询订单
$c = new LoginController();
$json =json_decode(file_get_contents("php://input"),true);
$osn = date('Ymd') . str_pad(mt_rand(1, 99999), 5, 'c', STR_PAD_LEFT);
$url = 'https://api.mch.weixin.qq.com/pay/orderquery';
$urlarr = parse_url($url);
$data = array();

$data['nonce_str'] = $c->getRanStr();
$data['appid'] = $c->appid;
$data['mchid'] = $c->mchid;
$data['out_trade_no'] = $json['out_trade_no'];//订单编号

$signStr = "appid=".$c->appid."&mch_id=".$c->mchid."&out_trade_no=".$json['out_trade_no']."&nonce_str=".$data['nonce_str'];
$data['sign'] = $c->getSha256WithRSA($signStr,"");

$data = json_encode($data);

$header  = array(
  'Content-Type:application/json; charset=UTF-8',
  'Accept:application/json',
  'User-Agent:'."WXPaySDK/3.0.10 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$c->mchid
);
$ret = $c->model->curl_post_https($url,$data,$header);
var_dump($ret);
exit;
$ret = json_decode($ret,true);
// var_dump($ret);
// exit;
$arr__ = array(
  "out_trade_no"=>$osn,
  "package"=>"prepay_id=".$ret['prepay_id'],
  "timeStamp"=>(string)$time__,
  "nonceStr"=>$randstr__,
  "signType"=>"RSA",
  "paySign"=> $paySign
);
$c->model->getJsonData(1,'success',$arr__);
