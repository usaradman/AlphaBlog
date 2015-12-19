<?php

define('ABSPATH',dirname(dirname(dirname(__FILE__))));

$basicConfig = array(
	'keywords'                 =>'Marvel Code, Alpha Blog, Blog, Usar',
	'description'              =>'Marvel Code, Alpha Blog, Blog, Usar',
    'LOAD_EXT_FILE'=>'function,extend',
    'URL_HTML_SUFFIX'           =>'html',
	'URL_MODEL'                =>2,
    'TMPL_L_DELIM'              =>'<{',
    'TMPL_R_DELIM'              =>'}>',
    'SHOW_PAGE_TRACE'           =>false,        //开启页面Trace
    'URL_ROUTER_ON'             =>true,       //开启路由
    'URL_CASE_INSENSITIVE'      =>false,        //大小写不敏感
    'URL_ROUTE_RULES'           =>array(
                'page/:page\d$'                         =>  'Index/index',
                'Article/:id\d$'                        => 'Article/index',
                'article/:id\d$'                        => 'Article/index',
                'article/add'                           => 'Article/add',
                'Articles/:type$'                       => 'Articles/index',
                'articles/:type$'                       => 'Articles/index',
                'Articles/:type/:typevalue$'            => 'Articles/index',
                'articles/:type/:typevalue$'            => 'Articles/index',
                'UserHome/articles'                     => 'UserHome/articles',
                'UserHome/manage'                       => 'UserHome/manage',
                'UserHome/page/:page$'                  => 'UserHome/index',
                'userhome/page/:page$'                  => 'UserHome/index',
                'user$'                                 => 'User/index?id=-1',
                'user/:id\d$'                           => 'User/index',
                'login$'                                => 'Login/index',
                'regist$'                               => 'Login/regist',
                'logout$'                               => 'Login/logout',
                //'admin'                                 => 'Charge/index.html',
             ),
    'DEFAULT_THEME'  => 'default',
    'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
);


$DBConfig = array(
	'DB_TYPE'               => 'mysql',     			// 数据库类型
    'DB_HOST'               => 'localhost',				// 服务器地址
    'DB_NAME'               => 'insistblog',          	// 数据库名
    'DB_USER'               => 'root',      			// 用户名
    'DB_PWD'                => '000000',    			// 密码
    'DB_PORT'               => '3306',       	 		// 端口
    'DB_PREFIX'             => 'usar_',    				// 数据库表前缀	
);

return array_merge($basicConfig, $DBConfig);
?>