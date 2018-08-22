<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
header('Content-type:text/html;charset=utf-8');
//允许跨域请求 , 是HTML5提供的方法，对跨域访问提供了很好的支持
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods:POST,GET,OPTIONS");
// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 开启调试模式
define('APP_DEBUG', true);
define('BIND_MODULE','experts');

define('ROOT_PATH',dirname(__FILE__)."/");
define('UPLOAD_PATH',ROOT_PATH."Upload/");

// 加载框架引导文件
require __DIR__ . '/../thinkphp/start.php';

//\think\Route::bind('module','index');

//\think\App::run();
