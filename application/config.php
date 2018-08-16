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
    // // 默认模块名
    // 'default_module'        => 'admin',
    // // 默认控制器名
    // 'default_controller'    => 'Index',
    // // 默认操作名
    // 'default_action'        => 'index',
    // 模版页面字符串替换
    'view_replace_str'      => [
        '__PUBLIC__' => '/Public',
    ],
    
    // 是否开启路由
    'url_route_on'          => true,
    // 日志类型
    'log'                   => [
        'type' => 'file', // 支持 socket trace file
    ],
    'app_debug'             => true,
    'url_html_suffix' => 'html',
     // URL普通方式参数 用于自动生成
    'url_common_param'       => true,
  
    // 'session'               => [
    //     'prefix'       => 'tbp',
    //     'type'         => 'redis',
    //     'auto_start'   => true,
    //     'host'         => '127.0.0.1', //'r-2ze3e02dbade9544.redis.rds.aliyuncs.com',   // redis主机
    //     'port'         => 6379, // redis端口
    //     //'password'     => 'UKB7Vt8sX9j4vR36mlaW',               // 密码
    //     'select'       => 5,
    //     'persistent'   => false,
    //     'session_name' => 'tbp:',
    //     'expire'       => 86400 * 3,
    // ],
    'desKey'                => '20(*^jh*', // cookie 加密key
    'locked_timeout'        => 10, // 锁等待时间
    'data_cache'            => true, // 是否开启缓存
    'password_pre'          => 'passpre_0208$#@!', // 密码前缀
    'check_pre'             => 'checkpre_2017!(*^', // 密码前缀第一次加密前缀

    'exception_ignore_type' => E_WARNING | E_USER_WARNING | E_NOTICE | E_USER_NOTICE,
    'cal'=>['内科','　　呼吸内科','　　消化内科','　　心血管内科','　　肾内科','　　血液内科','　　神经内科','　　内分泌内科','　　风湿免疫科','　　感染科','外科','　　骨外科','　　普外科','　　血管外科','　　神经外科','　　泌尿外科','　　心脏外科','　　胸外科','妇产科','儿科','其他科','　　肿瘤科','　　耳鼻喉科','　　眼科','　　皮肤科','　　性病科','心理科'],
    'cal_array'             => [['id' => 1, '内科', 'pid' => 0], ['id' => 2, '　　呼吸内科', 'pid' => 1], ['id' => 3, '　　消化内科', 'pid' => 1], ['id' => 4, '　　心血管内科', 'pid' => 1], ['id' => 5, '　　肾内科', 'pid' => 1], ['id' => 6, '　　血液内科', 'pid' => 1], ['id' => 7, '　　神经内科', 'pid' => 1], ['id' => 8, '　　内分泌内科', 'pid' => 1], ['id' => 9, '　　风湿免疫科', 'pid' => 1], ['id' => 10, '　　感染科', 'pid' => 1], ['id' => 11, '外科', 'pid' => 0], ['id' => 12, '　　骨外科', 'pid' => 11], ['id' => 13, '　　普外科', 'pid' => 11], ['id' => 14, '　　血管外科', 'pid' => 11], ['id' => 15, '　　神经外科', 'pid' => 11], ['id' => 16, '　　泌尿外科', 'pid' => 11], ['id' => 17, '　　心脏外科', 'pid' => 11], ['id' => 18, '　　胸外科', 'pid' => 11], ['id' => 19, '妇产科', 'pid' => 0], ['id' => 20, '儿科', 'pid' => 0], ['id' => 21, '其他科', 'pid' => 0], ['id' => 22, '　　肿瘤科', 'pid' => 21], ['id' => 23, '　　耳鼻喉科', 'pid' => 21], ['id' => 24, '　　眼科', 'pid' => 21], ['id' => 25, '　　皮肤科', 'pid' => 21], ['id' => 26, '　　性病科', 'pid' => 21], ['id' => 27, '   心理科', 'pid' => 21]],
    'IMG_DOMAIN'            => 'http://user.hospital.com',

];
