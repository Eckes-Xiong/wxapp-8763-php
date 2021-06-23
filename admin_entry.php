<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
header("Content-Type:text/html;charset=utf-8");
if( $_SERVER['HTTP_REFERER'] == "" ){
  header("Location:https://www.eckes.top/"); 
  exit;
}
//设置时区
date_default_timezone_set('PRC');
//路径配置
define('HOME_PATH',$_SERVER['DOCUMENT_ROOT']);//根目录
define('ADMIN_PATH', HOME_PATH.'/eckes_admin');//后台目录
//define('COMMON_PATH', HOME_PATH.'/common');//
define('APP_PATH', ADMIN_PATH.'/php');//应用目录

define('CONFIG_PATH', APP_PATH.'/config');//config目录
define('BASE_PATH', APP_PATH.'/base');//Base目录

define('MODEL_PATH', APP_PATH.'/model');//Model目录
define('VIEW_PATH', APP_PATH.'/view');//view目录
define('CONTROLLER_PATH', APP_PATH.'/controller');//Controller目录
//define('LIB_PATH', APP_PATH.'/lib');//lib目录
//导入框架基础类
require BASE_PATH.'/Base.php';
//实例化框架基础类
$app = new Base();
//加载类
$app->run();