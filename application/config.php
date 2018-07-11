<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    // 使用自己定义的过滤函数
    'default_filter'        => 'removeXSS',
    // 默认模块名
    'default_module'        => 'admin',
    // 默认控制器名
    'default_controller'    => 'Index',
    // 默认操作名
    'default_action'        => 'index',
    // 模版页面字符串替换
    'view_replace_str'      => [
        '__PUBLIC__' => '/Public',
    ],
    // 路径类型
    'url_common_param'      => true,
    // 是否开启路由
    'url_route_on'          => true,
    // 日志类型
    'log'                   => [
        'type' => 'file', // 支持 socket trace file
    ],
    'app_debug'             => true,
    //
    'session'               => [
        'prefix'       => 'tbp',
        'type'         => 'redis',
        'auto_start'   => true,
        'host'         => '127.0.0.1', //'r-2ze3e02dbade9544.redis.rds.aliyuncs.com',   // redis主机
        'port'         => 6379, // redis端口
        //'password'     => 'UKB7Vt8sX9j4vR36mlaW',               // 密码
        'select'       => 5,
        'persistent'   => false,
        'session_name' => 'tbp:',
        'expire'       => 86400 * 3,
    ],
    'desKey'                => '20(*^jh*', // cookie 加密key
    'locked_timeout'        => 10, // 锁等待时间
    'data_cache'            => true, // 是否开启缓存
    'password_pre'          => 'passpre_0208$#@!', // 密码前缀
    'check_pre'             => 'checkpre_2017!(*^', // 密码前缀第一次加密前缀

    'exception_ignore_type' => E_WARNING | E_USER_WARNING | E_NOTICE | E_USER_NOTICE,
    'IMG_DOMAIN' =>'http://user.hospital.com'
];
