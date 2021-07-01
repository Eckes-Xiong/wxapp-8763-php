<?php
/**
 * 公共模型类，便于自定义Model调用Db
 * 主要完成数据库连接，封装通用操作方法
 */
header('Content-Type: text/html; charset=utf-8');
class Model {
    //protected只给本类和子类使用
    protected $db=null;

    public $tb=null;
    public $num = null;
    public $insertId = null;
    /* @$html[]
     * 储存页面head数据
     * */
    public $html = null;

    public function __construct($tb=null){
        $this->tb = $tb;
        //完成数据库连接。
        $this->init();
    }

    private function init(){
        //初始化Db
        $this->db = Db::getInstance($GLOBALS['config']['db']);
    }

    /* @getCount()
     * 查询数据条数
     * */
    public function getCount($field, $where=''){
        $sql = 'SELECT '.$field.' FROM '.$this->tb.' '.$where;
        return $this->db->fetch($sql);
    }
    /* @exec()
     * 通用操作
     * */
    public function exec($sql){
        $this->db->exec($sql);
        $this->insertId = $this->db->lastId;
        //操作的数据条数
        $this->num = $this->db->num;
    }
    /* @getOne()
     * 查询单条数据
     * */
    public function getsqlOne($sql){
        return $this->db->fetch($sql);
    }
    /* @getAll()
     * 通用查询，全部数据
     * */
    public function getAll(){
        $sql = 'SELECT * FROM '.$this->tb;
        //echo '<script>console.log("getAll()'.$sql.'")</script>';
        return $this->db->fetchAll($sql);
    }
    /* @getSqlAll()
     * 通过sql查询所有数据
     * */
    public function getSqlAll($sql){
        return $this->db->fetchAll($sql);
    }
    /* @getFieldRandom()
     * 通用查询，随机单条数据
     * @params
     *   $fieldName 字段名
     *   $field 字段值
     * @return row[]
     * */
    public function getFieldRandom(){
        $sql = 'SELECT * FROM '.$this->tb.' AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM '.$this->tb.')-(SELECT MIN(id) FROM '.$this->tb.'))+(SELECT MIN(id) FROM '.$this->tb.')) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT 1';
        return $this->db->fetch($sql);
    }
    /* @getField()
     * 通用查询，单条数据
     * @params
     *   $fieldName 字段名
     *   $field 字段值
     * @return row[]
     * */
    public function getField($fieldName,$field,$type='='){
        $sql = 'SELECT * FROM '.$this->tb.' WHERE '.$fieldName.$type.'"'.$field.'"';
        return $this->db->fetch($sql);
    }
    public function getFieldAll($fieldName,$field,$type='='){
        $sql = 'SELECT * FROM '.$this->tb.' WHERE '.$fieldName.$type.'"'.$field.'"';
        return $this->db->fetchAll($sql);
    }
    /* @getHtml()
     * 获取网页中的title，keyword，description等数据
     * @table
     *   数据表：Base.php ->loadConfig() ->$GLOBALS['config']['app']['html_tb']
     * @params
     *   $fieldName 字段名
     *   $value 字段值
     * */
    public function getHtml($fieldName,$value){
        $tb = $GLOBALS['config']['app']['html_tb'];
        $sql = 'SELECT * FROM '.$tb.' WHERE '.$fieldName.'=\''.$value.'\'';
        //echo '<script>console.log("getHtml():'.$sql.'")</script>';
        return $this->db->fetch($sql);
    }

    /* @getView()
     *  导入网站模版
     * @params
     *   $sign 获取数据表中的数据指针[对应的数据表名称]
     *   $view 当前模块的模版名称
     * */
    public function getView($sign,$view){
        //获取html数据
        $this->html = $this->getHtml('h_sign',$sign);
        $navlist = $this->db->fetchAll("SELECT * FROM ".$GLOBALS['config']['tb']['nav_a_list']);
        //通用html head
        require_once VIEW_PATH.'/'.$GLOBALS['config']['app']['html_head_view'].'.php';
        //page body
        require_once VIEW_PATH.'/'.PLATEFORM.'/'.$view.'.php';
        //通用html foot
        require_once VIEW_PATH.'/'.$GLOBALS['config']['app']['html_foot_view'].'.php';
    }
    /* @pregSpace()
     *  替换字符串
     *  默认 去除多余空格
     * @params
     *   $pattern : 需要匹配的正则表达式
     *   $replace : 替换成的表达式
     *   $arr     : 需要替换的字符串 或 数组
     * */
    public function pregReplace($arr, $pa='/\s\s+/', $re=' '){
        return preg_replace($pa,$re,$arr);
    }
    public function writeLog($content=""){
        $this->exec("INSERT INTO wxapp_log (content) VALUES ('{$content}')");
    }
    public function writeIntegralLog($integral="", $remark="", $openid=""){
        $openid=$openid==""?$_SESSION["openid"]:$openid;
        $this->exec("INSERT INTO wxapp_log_integral ( integral, remark, openid ) VALUES ('{$integral}', '{$remark}', '{$openid}')");
    }
    public function writePayLog($amount="", $remark="", $openid=""){
        $openid=$openid==""?$_SESSION["openid"]:$openid;
        $this->exec("INSERT INTO wxapp_log_pay ( amount, remark, openid ) VALUES ('{$amount}', '{$remark}', '{$openid}')");
    }
    /* @getJsonData()
     *  组织数据结构
     *  默认 去除多余空格
     * @params
     *   $code : code
     *   $message : message
     *   $data    : data
     * */
    public function getJsonData($code,$message = '',$data = array(),$total='')
    {
        if (!is_numeric($code)){
            return '错误';
        }
        $result = array(
            'code' => $code,
            'message' => $message,
            'total' => $total,
            'data' => $data
        );
        echo json_encode($result);
        exit;
    }
    /* @json2Array()
     *  json to array
     * */
    public function json2Array($json)
    {
        return json_decode($json,true);
    }

    public function curl_post_https($url,$data,$header,$hasPEM){ // 模拟提交数据函数
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1); // 从证书中检查SSL加密算法是否存在
        //curl_setopt($curl, CURLOPT_USERAGENT, $header[2]); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if($hasPEM==true){
            //默认格式为PEM，可以注释
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, dirname(__FILE__).'/8763_vapp_zs_cccc.pem');
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, dirname(__FILE__).'/8763_vapp_zs_kkkk.pem');
        }
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_HTTPHEADER,$header);//设置HTTP头
        $tmpInfo = curl_exec($curl); // 执行操作
        if (curl_errno($curl)) {
            echo 'Errno'.curl_error($curl);//捕抓异常
        }
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式
    }

    //set 成就
    public function setAchieveUser($aid){
        $count = $this->getsqlOne("SELECT COUNT(1) from wxapp_achieve_user WHERE openid='{$_SESSION['openid']}' AND aid='{$aid}'");
        if($count["COUNT(1)"]==0){
            $this->exec("INSERT INTO wxapp_achieve_user (openid, aid) VALUES ('".$_SESSION['openid']."', {$aid})");
        }
    }

    //发送订阅消息
    public function wxSendTemplateMessage($arr=array()){
        $sql_s = 'https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token='.$arr["access_token"];
        $data = array(
            "touser"=>$arr["openid"],
            "template_id"=>$arr["tempid"],
            "miniprogram_state"=>($arr["state"]||'formal'),
            "lang"=>"zh_CN",
            "data"=>$arr["data"]
        );
        $data = json_encode($data);
        $header  = array();
        $this->curl_post_https($sql_s,$data,$header);
    }   
    
	public function checkWxToken($header){
		if($header["token"]!=$_SESSION['token']){
			http_response_code(401);
			exit(json_encode(array(
				'code' => 401,
				'message' => '登录验证失败！',
				'data' => array(
                    "header"=> $header["token"],
                    "session"=> $_SESSION
                )
			), JSON_UNESCAPED_UNICODE));
			exit;
		}
	}
} 