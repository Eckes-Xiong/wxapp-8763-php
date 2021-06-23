<?php
class WxController {

    public $appid  = 'wxee7c129515996768';
    public $secret = '8a1b472c7fbf698f886e3753c1a03678';
    public $model = null;

    public function __construct(){
        //实例化model
        $this->model = new WxModel('wx_swiper_list');
    }

    //小程序 用户登录之后获取openid和session_key
    public function getOpenidAndSessionKey($code,$type='authorization_code'){
        $api    = 'https://api.weixin.qq.com/sns/jscode2session';
        $res = file_get_contents($api.'?appid='.$this->appid.'&secret='.$this->secret.'&js_code='.$code.'&grant_type='.$type);
        $arr = json_decode($res,true);
        $_SESSION['openid']=$arr['openid'];
        $_SESSION['session_key']=$arr['session_key'];
        return $res;
    }

    //小程序 获取 access_token
    public function getAccessToken($now){
        $now = $now?$now:round(microtime(true)*1000);
        $api  = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential';
        $res = file_get_contents($api.'&appid='.$this->appid.'&secret='.$this->secret);
        $arr = json_decode($res,true);
        $_SESSION['access_token']=$arr['access_token']; // 请求微信小程序官方接口时的凭证
        $_SESSION['expires_in']=$arr['expires_in']; // 有效持续时间
        $_SESSION['access_token_time']=$now+$arr['expires_in']*1000-20000;// 到期时间提前20秒
        return $arr;
    }
    //小程序 判断 Access_token 是否过期
    public function resetAccessTokenTime(){
        $now = round(microtime(true)*1000);
        if($now > $_SESSION['access_token_time']){
            echo 'overdue access_token';
            exit();
        }
    }
    //php发送post请求
    public function toPost($url,$json_data){
        //curl方式发送请求
        $ch = curl_init();
        //设置请求为post
        curl_setopt($ch, CURLOPT_POST, 1);
        //请求地址
        curl_setopt($ch, CURLOPT_URL, $url);
        //json的数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //请求头定义为json数据
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type:application/json;charset=utf-8'
            //,'Content-Length: '.strlen($json_data)
            )
        );
        $response = curl_exec($ch);
        //$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        echo $response;
    }
    //小程序  发送模板消息
    public function postSubscribeMsg($json_data){
        $this->resetAccessTokenTime();
        //
        $url = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token='.$_SESSION['access_token'];

        $this->toPost($url,$json_data);
    }

    /** 
     * Model
     * */ 
    public function getSlideItems(){
        $all = $this->model->getSlideItems();
        return $this->model->getJsonData(1,'',$all);
    }
} 