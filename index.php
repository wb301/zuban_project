<?php
defined('YII_MODULE_LIST') or define('YII_MODULE_LIST', 'format,users,dashboard');
$env = isset($_SERVER['ITEM_ENV']) ? $_SERVER['ITEM_ENV'] : 'prod';
defined('YII_ENV') or define('YII_ENV', $env);
//YII_ENV取值 prod(线上） test(测试) dev(开发)
//E_ALL & ~E_NOTICE,E_ERROR
(YII_ENV === 'dev' || YII_ENV === 'test') && (defined('YII_DEBUG') or define('YII_DEBUG', true)) && error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

//获取项目名称
$scriptName = explode('/',$_SERVER['SCRIPT_NAME']);
$_SERVER['ITEM_NAME'] = $scriptName[1];

//获取域名key 区分两种情况 m.me-city.com  moomoo.m.yunfun.com。本地环境采用m.metersbonws-user.com
$hostList = explode('.',$_SERVER['HTTP_HOST']);
if( YII_ENV === 'dev' ){
	$sellerDomain = explode('-',$hostList[1])[0];
}else if( count($hostList) == 4 ){
	$sellerDomain = $hostList[0];
}else if( count($hostList) == 3 ){
	$sellerDomain = $hostList[1];
}else if( !empty($_REQUEST['_scd']) ){
	$sellerDomain = $_REQUEST['_scd'];
}else{
	$sellerDomain = $_SERVER['ITEM_NAME'];
}
//用于读取不同域名配置的常量
defined('SELLER_DOMAIN') or define('SELLER_DOMAIN',$sellerDomain);

//判断进入不同的模块
$moduleName = array_slice( explode('/',$_SERVER['PHP_SELF']),-3,1 );
$moduleName = $moduleName[0] ? $moduleName[0] : 'format';
defined('APP_NAME') or define('APP_NAME', $moduleName);

//路由相关，出去目录一级
$_SERVER['SCRIPT_NAME'] = '/'.$scriptName[2];
$strpos = strpos($_SERVER['REQUEST_URI'],APP_NAME) === false ? strpos($_SERVER['REQUEST_URI'],$_SERVER['ITEM_NAME'])+strlen($_SERVER['ITEM_NAME']) : strpos($_SERVER['REQUEST_URI'],APP_NAME)+strlen(APP_NAME);
$_SERVER['REQUEST_URI'] = substr( $_SERVER['REQUEST_URI'],$strpos );

//获取域名
$baseDomain = substr($_SERVER['HTTP_HOST'], strrpos($_SERVER['HTTP_HOST'], '.', -5) + 1);
define('BASE_DOMAIN', $baseDomain);

//载入配置
require(__DIR__ . '/vendor/autoload.php');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/common/config/bootstrap.php');
require(__DIR__ . '/' . APP_NAME . '/config/bootstrap.php');
$config = yii\helpers\ArrayHelper::merge(
	require(__DIR__ . '/common/config/default/main.php'),
	require(__DIR__ . '/common/config/default/main-' . YII_ENV . '.php'),
	require(__DIR__ . '/' . APP_NAME . '/config/main.php')
);

//运行
$application = new yii\web\Application($config);
$application->run();
