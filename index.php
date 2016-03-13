<?php
	@session_start();
	// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
	define('APP_DEBUG',true);

	define('THINK_PATH', './ThinkPHP/');
	define('APP_NAME', 'Home');
	define('APP_PATH', './Home/');

	
	require THINK_PATH . 'ThinkPHP.php';
	
	//TODO 首先要检查安装，未安装的话跳转到安装界面，安装数据库和建立目录

?>