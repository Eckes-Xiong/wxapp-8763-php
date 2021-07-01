<?php
class Base {
    //run方法，完成所有功能
    public function run(){
        //加载配置
        $this->loadConfig();
        //获取参数
        $this->getRequestParams();
        //自动加载
        $this->registerAutoLoad();
        //请求分发
        //$this->dispatch();
    }

    //
    private function loadConfig(){
        //类中其他方法中使用
        $GLOBALS['config'] = require CONFIG_PATH.'/config.db.php';
    }

    //创建自定义加载方法
    //标准类库使用
    public function userAutoLoad($className){
        //定义基本类的列表，也就是框架中的类列表
        $baseClass = array(
            'Model' => BASE_PATH.'/Model.php'
            ,'Db' => BASE_PATH.'/Db.php'
        );

        //判断基本类类型与加载
        if(isset($baseClass[$className])){
            //加载基类Model, Db
            require $baseClass[$className];
        }else if(substr($className,-5) == 'Model'){
            require MODEL_PATH.'/'.PLATEFORM.'/'.$className.'.php';
        }else if(substr($className,-10) == 'Controller'){
            require CONTROLLER_PATH.'/'.PLATEFORM.'/'.$className.'.php';
        }
    }

    //注册自动加载方法
    private function registerAutoLoad(){
        spl_autoload_register(array($this,'userAutoLoad'));
    }

    //获取请求参数
    private function getRequestParams(){

        if(isset($_SESSION['eckes_cn_user'])
            && isset($_SESSION['eckes_cn_uid'])
            && $_SESSION['eckes_cn_uid'] == md5($_SESSION['eckes_cn_user']."eckes.cn.1989")
        ){
            //获取当前模块 home or admin
            $defPlate = $GLOBALS['config']['app']['default_plateform'];
            // url-> ?p ,当前值后面还要用，使用常量
            $p = isset($_GET['p']) ? $_GET['p'] : $defPlate;
            define('PLATEFORM',$p);

            //获取当前控制器
            $defController = $GLOBALS['config']['app']['default_controller'];
            // url-> ?c
            $c = isset($_GET['c']) ? $_GET['c'] : $defController;
            define('CONTROLLER',$c);

            //获取当前方法
            $defMethod = $GLOBALS['config']['app']['default_method'];
            // url-> ?m
            $m = isset($_GET['m']) ? $_GET['m'] : $defMethod;
            define('METHOD',$m);

            //获取当前数据表
            $defTable = $GLOBALS['config']['app']['default_tb'];
            // url-> ?m
            $t = isset($_GET['t']) ? $GLOBALS['config']['app'][$_GET['t']] : $defTable;
            define('TABLE',$t);

            //获取当前视图view
            $defView = $GLOBALS['config']['app']['default_view'];
            // url-> ?m
            $v = isset($_GET['t']) ? $GLOBALS['config']['app'][$_GET['v']] : $defView;
            define('VIEW',$v);
        }else{
            define('PLATEFORM',$GLOBALS['config']['app']['default_plateform']);
            define('METHOD',$GLOBALS['config']['app']['default_method']);

            define('CONTROLLER',$GLOBALS['config']['app']['login_controller']);
            define('TABLE',$GLOBALS['config']['app']['login_tb']);
            define('VIEW',$GLOBALS['config']['app']['login_view']);
        }
    }

    public function dispatch(){
        //获取控制器,实例化控制器类
        $controllerName = CONTROLLER.'Controller';
        $controller = new $controllerName(TABLE,VIEW);

        //调用当前方法
        $methodName = METHOD.'Action';
        $controller->$methodName();
    }

    /**工具方法**/
	public function isLogin(){
		if(!$_SESSION['u_id']){
			http_response_code(401);
			exit(json_encode(array(
				'code' => 401,
				'message' => '未登录',
				'data' => []
			), JSON_UNESCAPED_UNICODE));
		}
	}
    public function jsonEncode($json){
        return json_encode($json);
    }
    public function jsonDecode($json){
        return json_decode($json);
    }
    public function log($param){
        if(is_array($param)){
            echo '<script>console.log('.$param.');</script>';
        }else{
            echo '<script>console.log("'.$param.'");</script>';
        }
    }
} 