<?php
	@session_start();
	// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
	define('APP_DEBUG',true);

	define('THINK_PATH', './ThinkPHP/');
	define('APP_NAME', 'Home');
	define('APP_PATH', './Home/');

	
	require THINK_PATH . 'ThinkPHP.php';
	

?>