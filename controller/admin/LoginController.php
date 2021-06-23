<?php
class LoginController {

    public $model = null;
    public $table = null;
    public $view = null;
	public $appid  = 'wx45c8d1b497f3b936';
	public $mchid  = '1596068181';
    public $secret = 'ed3cdb1ec8dbd376a903cacc17566b62';
    public $xlid = '7DBD79560C226AE973AF5E7550F465626B6153C6';//api证书序列号
    public $apikey = 'ed3cdb1ec8dbd376a903cacc17566ccc';//api证书key

    public function __construct($table,$view){
        //实例化model
        $this->table = $table?$table:'top_user';
        $this->view = $view;
        $this->model = new LoginModel($this->table);
    }
    //小程序 用户登录之后获取 接口调用凭据（access_token）
    //&appid=APPID&secret=APPSECRET
    public function getAccessToken(){
        $now = time();
        $at = $this->model->getSqlOne("SELECT at,ei,updateTime FROM wxapp_at");
        if($now > strtotime($at["updateTime"])+$at["ei"]-1000){
            $api = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
            $res = file_get_contents($api.'&secret='.$this->secret.'&appid='.$this->appid);
            $arr = json_decode($res,true);
            if($arr['errcode']){
                echo $arr['errcode'];
                exit();
            }
            $this->model->exec("UPDATE wxapp_at SET at='{$arr['access_token']}', ei='{$arr['expires_in']}, number=number+1 WHERE id=1'");
            return $arr['access_token'];
            exit();
        }
        return $at['at'];
    }

    //小程序 用户登录之后获取openid和session_key
    public function getOpenidAndSessionKey($code,$type='authorization_code'){
        $api    = 'https://api.weixin.qq.com/sns/jscode2session';
        $res = file_get_contents($api.'?appid='.$this->appid.'&secret='.$this->secret.'&js_code='.$code.'&grant_type='.$type);
        $arr = json_decode($res,true);
		if($arr['errcode']==40163){
			echo "code been used";
			exit();
		}
        $_SESSION['openid']=$arr['openid'];
        $_SESSION['session_key']=$arr['session_key'];
        //return $res;
    }

    //初始化当前视图view
    public function showPageAction(){
        $this->model->getView($this->table,$this->view);
    }

    //查询注册记录是否存在, 不存在返回0
    public function toTestRecord($fieldName,$field){
        $where = "where ".$fieldName."='".$field."'";
        return $this->model->getCount($fieldName, $where);
    }
    //添加数据
    public function toInsert($fields){
        $this->model->getExec($fields);
    }
    //登录验证
    public function toSignIn($fields){
		$fields = json_decode($fields);
        $num = $this->model->getSign($fields);
        if(!empty($num)){
            $_SESSION['u_id']=md5($fields->username.'|eckes.top|'.$fields->password);
            $_SESSION['username']=$fields->username;
            $_SESSION['u_privite']=$num->privite;
            return $this->model->getJsonData(1,'登陆成功');
        }else{
            return $this->model->getJsonData(10103,'账号或密码错误！');
        }
    }
    //注销
    public function toSignOut(){
        unset($_SESSION['u_id']);
        session_destroy();
        return $this->model->getJsonData(1,'注销成功！');
    }
    //获取缓存状态//和数据
    public function getSession(){
        if($_SESSION['u_id']){
            $data = $this->model->getField('u_id',$_SESSION['u_id']);
            return $this->model->getJsonData(1,'欢迎回来',$data);
        }else{
            header('status: 401 Unauthorized');
            exit();
        }
    }
	//微信小程序 返回token
	public function toGetWxToken($fields){
		$fields = json_decode($fields);
		
		$this->getOpenidAndSessionKey($fields->code);
		
		$_SESSION['token'] = md5($fields->code).'.'.md5(date("h:i:sa")."eckes.top.2021");
		
		$data['token'] = $_SESSION['token'];
		$result = array(
			'code' => 1,
			'message' => "success",
			'data' => $data
		);
		echo json_encode($result);
		exit();
	}
	//微信登录 获取用户信息
    public function toGetWxUserInfo($json){

        $num = $this->model->getsqlOne("SELECT id,nickName FROM wxapp_user WHERE openid='{$_SESSION['openid']}'");
        $cartNum = $this->model->getsqlOne("SELECT COUNT(1) FROM wxapp_order WHERE status=0 AND buyerOpenid='{$_SESSION['openid']}'");

		if(empty($num)){
            $this->model->exec("INSERT INTO wxapp_user (openid) VALUES ('".$_SESSION['openid']."')");
            $this->model->exec("INSERT INTO wxapp_task_user (openid) VALUES ('".$_SESSION['openid']."')");

            $data = $this->model->getsqlOne("SELECT id FROM wxapp_user WHERE openid='{$_SESSION['openid']}'");
            $data=array(
                "id"=>$data["id"],
                "nickName"=>"",
                "avatarUrl"=>"",
                "level"=>"0",
                "levelTime"=>date("Y-m-d H:i:s"),
                "orderNum"=>"0",
                "collectNum"=>"0",
                "expNum"=>"0",
                "isVip"=>"0",
                "integralNum"=>"0",
                "isStore"=>"0",
                "qrcode"=>"",
                "phone"=>"0"
            );
            $this->model->writeLog("{$_SESSION['openid']} 新赠用户");

		}else{
            if($json["nickName"]){
                $this->model->exec("UPDATE wxapp_user SET nickName='{$json["nickName"]}',avatarUrl='{$json["avatarUrl"]}' WHERE openid='{$_SESSION['openid']}'");
                $this->model->writeLog("{$_SESSION['openid']} 用户授权用户名，头像");
            }
            $num = $this->model->getsqlOne("SELECT qrcode,id,levelTime,phone,nickName,avatarUrl,level,orderNum,collectNum,expNum,isVip,integralNum,isStore FROM wxapp_user WHERE openid='{$_SESSION['openid']}'");
            $data = $num;
            $data["phone"] = $data["phone"]?"1":"0";
        }
        
        if($data["isVip"]=="1"){
            $now = strtotime(date("Y-m-d H:i:s"));
            $vipTime = strtotime(date($data["levelTime"]));

            if($now>=$vipTime){
                $this->model->exec("UPDATE wxapp_user SET isVip=0 WHERE openid='{$_SESSION['openid']}'");
            }
        }

        $data["cartNum"] = $cartNum["COUNT(1)"];
        $data["plf"] = $json["plf"]!="release"?"-":"我要开店";
		$result = array(
			'code' => 1,
			'message' => "success1",
			'data' => $data
		);
		echo json_encode($result);
		exit();
    }

    //wxapi
    /* 退款 */
    public function refund($json){
        //对外暴露的退款接口
        $result = $this->wxrefundapi($json);
        return $result;
    }
    private function wxrefundapi($json){
        $url = 'https://api.mch.weixin.qq.com/v3/refund/domestic/refunds';
        $urlarr = parse_url($url);
        $time = time();
        $randstr = $this->getRanStr();
        //通过微信api进行退款流程
        $parma = array(
            "amount"=>array(
                "total"=>$json["amount"],
                "currency"=>"CNY",
                "refund"=>$json["amount"]
            ),
            'out_refund_no'=> 'refund_'.$json["no"],
            'out_trade_no'=> $json["no"],
        );
        $parma = json_encode($parma);

        $key = $this->getSign_($parma,$urlarr['path'],$randstr,$time);
        //$xmldata = $this->toXml($parma);

        $token = sprintf('mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $this->mchid, 
            $randstr, 
            $time, 
            $this->xlid, 
            $key
        );//头部信息

        $header  = array(
        'Content-Type:application/json; charset=UTF-8',
        'Accept:application/json',
        'User-Agent:'."WXPaySDK/3.0.10 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$this->mchid,
        'Authorization:WECHATPAY2-SHA256-RSA2048 '.$token
        );
        $result = $this->model->curl_post_https($url,$parma,$header,true);
        $result = json_decode($result,true);
        return $result;
    }
    /**
     * 模拟POST请求
     */
    public function createAuthorization( $url , $method = 'GET')
    {
        $str = "POST"."\n".$url."\n".$time."\n".$randstr."\n".$data."\n";
        $key = file_get_contents('apiclient_key.pem');//在商户平台下载的秘钥
        $str = $this->getSha256WithRSA($str,$key);
        return $str;
    }
    //生成 sha256WithRSA 签名
    public function getSha256WithRSA($str, $key){
        $key = openssl_pkey_get_private($key);
        openssl_sign($str,$signature,$key,"SHA256");
        openssl_free_key($key);
        $sign = base64_encode($signature);
        return $sign;
    }
    //微信支付签名
    public function getSign_($data=array(),$url,$randstr,$time){
        $str = "POST"."\n".$url."\n".$time."\n".$randstr."\n".$data."\n";
        $key = file_get_contents(dirname(__FILE__).'/8763_vapp_zs_kkkk.pem');//在商户平台下载的秘钥
        $str = $this->getSha256WithRSA($str,$key);
        return $str;
    }
    //生成 随机数
    public function getRanStr($length=32){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
        $randstr ="";
        for ( $i = 0; $i < $length; $i++ )  {  
            $randstr .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
        }
        return $randstr;
    }
    //调起支付的签名
    public function getWechartSign($ret,$randstr__,$time__){
        //$ret = json_decode($ret,true);        
        $str = $this->appid."\n".$time__."\n".$randstr__."\n"."prepay_id=".$ret['prepay_id']."\n";
        $key = file_get_contents(dirname(__FILE__).'/8763_vapp_zs_kkkk.pem');
        $str = $this->getSha256WithRSA($str,$key);
        return $str;
    }
    public function orderQuerySign($str){
        $key = file_get_contents(dirname(__FILE__).'/8763_vapp_zs_kkkk.pem');
        $str = $this->getSha256WithRSA($str,$key);
        return $str;
    }
    public function ToXml($data)
	{
		if(!is_array($data) || count($data) <= 0)
		{
    		return "";
    	}
    	
    	$xml = "<xml>";
    	foreach ($data as $key=>$val)
    	{
    		if (is_numeric($val)){
    			$xml.="<".$key.">".$val."</".$key.">";
    		}else{
    			$xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
    		}
        }
        $xml.="</xml>";
        return $xml; 
    }
    public function fromXml($xml)
    {
        // 禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        return json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
    }

    //构建 微信支付 data
    public function wxPay($json){
        $url = 'https://api.mch.weixin.qq.com/v3/pay/transactions/jsapi';
        $urlarr = parse_url($url);
        //"wxpayI"
        $osn = $json["osn"].date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $data = array();
        $time = time();
        $randstr = $this->getRanStr();

        $data['appid'] = $this->appid;
        $data['mchid'] = $this->mchid;
        $data['description'] = $json["description"];
        $data['out_trade_no'] = $osn;//订单编号
        $data['notify_url'] = "https://eckes.top/eckes_admin/php/wxapi/notify.php";//回调接口
        $data['amount']['total'] = $json['amount'];//24.8元*100 = 24800
        $data['payer']['openid'] = $_SESSION['openid'];

        $data = json_encode($data);
        $key = $this->getSign_($data,$urlarr['path'],$randstr,$time);//签名
        
        $token = sprintf('mchid="%s",serial_no="%s",nonce_str="%s",timestamp="%d",signature="%s"',$this->mchid,$this->xlid,$randstr,$time,$key);//头部信息

        $header  = array(
            'Content-Type:application/json; charset=UTF-8',
            'Accept:application/json',
            'User-Agent:'."WXPaySDK/3.0.10 (".PHP_OS.") PHP/".PHP_VERSION." CURL/".$this->mchid,
            'Authorization:WECHATPAY2-SHA256-RSA2048 '.$token
        );
        
        $ret = $this->model->curl_post_https($url,$data,$header);
        $ret = json_decode($ret,true);

        $randstr__ = $this->getRanStr();
        $time__ = time();
        $paySign = $this->getWechartSign($ret,$randstr__,$time__);

        $arr__ = array(
            "out_trade_no"=>$osn,
            "package"=>"prepay_id=".$ret['prepay_id'],
            "timeStamp"=>(string)$time__,
            "nonceStr"=>$randstr__,
            "signType"=>"RSA",
            "paySign"=> $paySign
        );
        // 积分
        if($json["osn"]=="wxpayI"){
            $cache_status =3;
        }
        // 月卡
        if($json["osn"]=="wxpayM"){
            $cache_status =2;
        }
        // 购物
        if($json["osn"]=="wxpay"){
            $cache_status = 1;
            $this->model->exec("UPDATE wxapp_order SET out_trade_no='{$osn}' WHERE id in({$json["oids"]})");
        }
        $this->model->exec("INSERT INTO wxapp_order_cart_cache ( status, openid, out_trade_no, oids, integral, _amount, couponId, address, amount, sid, addrType) VALUES ({$cache_status}, '{$_SESSION['openid']}', '{$osn}', '{$json["oids"]}', '{$json['integral']}', '{$json['_amount']}', '{$json['couponId']}', '{$json['address']}', '{$json['amount']}', '{$json['sid']}', '{$json['addrType']}')");
        $this->model->getJsonData(1,'success',$arr__);
    }
    //是否支付成功！
    public function isWxPaySuccess($out_trade_no, $status=1){
        $count = $this->model->getsqlOne("SELECT COUNT(1) FROM wxapp_order_cart_cache WHERE status='{$status}' AND openid='{$_SESSION['openid']}' AND out_trade_no='{$out_trade_no}'");
        if($count["COUNT(1)"]==0){
            $this->model->getJsonData(0,'支付异常');
            exit;
        }
    }
} 