<?php

/**
 *    获取缓存值
 * * @param string $cacheKey
 *            缓存key
 * @param number $make
 *            1:默认 2:强制不读缓存,且不设置缓存 3:刷新缓存
 * @return boolean unknown Ambigous NULL, multitype:>
 */
function getRedisCache($cacheKey = "", $make = 1)
{
    if (empty($cacheKey) || $make != 1) {
        return false;
    }

    //$cacheKey = 'key:'.md5($cacheKey);
    $dataCache                        = \think\Config::get("data_cache");
    $cacheType                        = 'redis';
    $dataCache == false && $cacheType = 'C';
    //开启缓存的状态下 是否有清除缓存的宏定义
    if (defined("CLEAR_CACHE_HANDLE") && CLEAR_CACHE_HANDLE && $dataCache) {
        return false;
    }

    if ($cacheType == 'C') {
        return \think\Config::get($cacheKey);
    } else {
        $list = \think\Cache::get($cacheKey);
        if ($list !== false) {
            return $list;
        }

        $locked = \app\common\util\RedisLock::lock($cacheKey, \think\Config::get("locked_timeout"));

        \think\Config::set("locked_status", $locked);
        if ($locked) {
            $list = \think\Cache::get($cacheKey);
            if ($list !== false) {
                return $list;
            }

        }
    }

    return false;
}

/**
 * 设置缓存值
 * @param string $cacheKey
 *            缓存key
 * @param string $cacheType
 *            缓存方法
 * @param string $value
 *            设置值
 * @param number $make
 *            1:默认 2:强制不读缓存,且不设置缓存 3:刷新缓存
 * @return boolean
 */
function setRedisCache($cacheKey = "", $value = "", $make = 1, $time = null)
{
    if (empty($cacheKey) || $make == 2) {
        return false;
    }

    //$cacheKey = 'key:'.md5($cacheKey);
    $dataCache                        = \think\Config::get("data_cache");
    $cacheType                        = 'redis';
    $dataCache == false && $cacheType = 'C';

    if ($cacheType == 'C') {
        \think\Config::set($cacheKey, $value);
    } else {
        \think\Cache::set($cacheKey, $value, $time === 0 ? "" : $time);
        \think\Config::get("locked_status") && \app\common\util\RedisLock::release($cacheKey);
    }
}

/**
 * 返回redis
 * @return \Redis
 */
function redis($name = "redis")
{
    static $redis_list = array();
    $config            = \think\Config::get($name);
    if (empty($redis_list[$name])) {
        $redis    = new \Redis();
        $pconnect = $config['persistent'];
        if ($pconnect) {
            $conn = $redis->pconnect($config['host'], $config['port'], $config['timeout'], $name);
        } else {
            $conn = $redis->connect($config['host'], $config['port'], $config['timeout']);
        }
        if (isset($config['password']) && $config['password'] != "") {
            $redis->auth($config['password']);
        }

        $db = $config['select'];
        $redis->select($db);
        $redis_list[$name] = $redis;
    }
    return $redis_list[$name];
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function getClientIp($type = 0, $adv = false)
{
    $type      = $type ? 1 : 0;
    static $ip = null;
    if ($ip !== null) {
        return $ip[$type];
    }

    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) {
                unset($arr[$pos]);
            }

            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

// 返回字符串hash
function MD5Hash($str)
{
    // 0.050
    $hash = md5($str);
    $hash = $hash[0] | ($hash[1] << 8) | ($hash[2] << 16) | ($hash[3] << 24) | ($hash[4] << 32) | ($hash[5] << 40) | ($hash[6] << 48) | ($hash[7] << 56);
    return $hash;
}

function OpenSSLHash($str)
{
    // 0.22
    $hash = 0;
    $n    = strlen($str);
    for ($i = 0; $i < $n; $i++) {
        $hash ^= (ord($str[$i]) << ($i & 0x0f));
    }
    return $hash;
}

function encrypt($string, $key)
{
    //加密用的密钥文件
    //$key = "l$`h.V,&";

    //加密方法
    $cipher_alg = MCRYPT_TRIPLEDES;
    //初始化向量来增加安全性
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);

    //开始加密
    $encrypted_string = mcrypt_encrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv);
    return base64_encode($encrypted_string); //转化成16进制
    //  return $encrypted_string;
}

function decrypt($string, $key)
{
    $string = base64_decode($string);
    //加密用的密钥文件
    //$key = "l$`h.V,&";

    //加密方法
    $cipher_alg = MCRYPT_TRIPLEDES;
    //初始化向量来增加安全性
    $iv = mcrypt_create_iv(mcrypt_get_iv_size($cipher_alg, MCRYPT_MODE_ECB), MCRYPT_RAND);

    //开始加密
    $decrypted_string = mcrypt_decrypt($cipher_alg, $key, $string, MCRYPT_MODE_ECB, $iv);
    return trim($decrypted_string);
}

//发送短信
function sendSMS($mobile, $content)
{
    $url     = config('MOBILE_CONFIG.API_URL'); //接口地址
    $content = iconv("utf-8", "gb2312//IGNORE", $content . config('MOBILE_CONFIG.SIGN_NAME'));
    $data    = array
        (
        'username' => config('MOBILE_CONFIG.USER_NAME'), //用户账号
        'password' => config('MOBILE_CONFIG.USER_PASS'), //密码
        'mobile'   => $mobile, //号码
        'content'  => $content, //内容
        'apikey'   => config('MOBILE_CONFIG.USER_KEY'), //apikey
    );

    $result = curlSMS($url, $data); //POST方式提交
    if (stripos($result, 'success') === 0) {
        return true;
    }
    return false;
}

function curlSMS($url, $post_fields = array())
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3600); //60秒
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'http://www.yourdomain.com');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    $data = curl_exec($ch);

    curl_close($ch);
    $res = explode("\r\n\r\n", $data);
    return isset($res[2]) ? $res[2] : '';
}

function curl($url, $post = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3600); //60秒
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_REFERER, 'http://www.yourdomain.com');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    $data = curl_exec($ch);

    curl_close($ch);

    $res = explode("\r\n\r\n", $data);
    return isset($res[2]) ? $res[2] : '';
}

/*
 * 字符串映射加密
 * $code = ''需要加密的串
 * $flag   true加密, false解密
 */
function strEncodeLH($code = '', $flag = true)
{
    $str       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-_@';
    $strEncode = 'pdJiPufKY2C3yFZXazD5Wc7etxLE-mVUqv1IAhO_lnBRg6b4wrS.GN9k@MjHT8Q0os';
    $sArr      = array();
    if ($flag) {
        for ($i = 0; $i < strlen($str); $i++) {
            $sArr[$str{$i}] = $strEncode{$i};
        }
    } else {
        for ($i = 0; $i < strlen($str); $i++) {
            $sArr[$strEncode{$i}] = $str{$i};
        }
    }
    $codeEncode = '';
    for ($i = 0; $i < strlen($code); $i++) {
        $codeEncode .= isset($sArr[$code{$i}]) ? $sArr[$code{$i}] : $code[$i];
    }
    return $codeEncode;
}

function xorEncode($buf)
{
    $pKey    = 'lh-encode-key';
    $iKeyLen = strlen($pKey);
    $k       = 0;
    $str     = '';
    $iLen    = strlen($buf);
    for ($i = 0; $i < $iLen; $i++) {
//        $buf[$i] = $buf[$i]^$pKey[$k];
        $str .= $buf[$i] ^ $pKey[$k];
        $k++;
        if ($k >= $iKeyLen) {
            $k = 0;
        }
    }
    return $str;
}

/**
 * 身份证加密解密
 * @param unknown $code
 * @param string $flag = true 加密 false 解密
 */
function idcardEncode($code, $flag = true)
{
    if ($flag) {
        $str = strEncodeLH($code);
        $str = xorEncode($str);
        return strtoupper(bin2hex($str));
    } else {
        try {
            $str = hex2bin($code);
        } catch (Exception $e) {
            return "";
        }
        $str = xorEncode($str);
        $str = strEncodeLH($str, false);
        return $str;
    }
}

// 手机号隐藏
function hidePhone($phone)
{
    $pattern     = '/(\d{3})(\d{4})(\d{4})/i';
    $replacement = '$1****$3';
    $phone       = preg_replace($pattern, $replacement, $phone);
    return $phone;
}

/**
 * 获取站点的图片地址目录 (域名目录)
 * @param unknown $siteid 站点ID
 * @return unknown
 */
function getImgPath()
{
    return \think\Config::get('UPLOAD_URL');
}

function config($name)
{
    return \think\Config::get($name);
}

function getImgDir($siteid = 0)
{
    return UPLOAD_PATH;
}

/**
 * 获取数字的阴历叫法
 * @param num 数字
 * @param isMonth 是否是月份的数字
 */
function getCapitalNum($num, $isMonth)
{
    $isMonth   = $isMonth || false;
    $dateHash  = array('0' => '', '1' => '一', '2' => '二', '3' => '三', '4' => '四', '5' => '五', '6' => '六', '7' => '七', '8' => '八', '9' => '九', '10' => '十 ');
    $monthHash = array('0' => '', '1' => '正月', '2' => '二月', '3' => '三月', '4' => '四月', '5' => '五月', '6' => '六月', '7' => '七月', '8' => '八月', '9' => '九月', '10' => '十月', '11' => '冬月', '12' => '腊月');
    $res       = '';
    if ($isMonth) {
        $res = $monthHash[$num];
    } else {
        if ($num <= 10) {
            $res = '初' . $dateHash[$num];
        } else if ($num > 10 && $num < 20) {
            $res = '十' . $dateHash[$num - 10];
        } else if ($num == 20) {
            $res = "二十";
        } else if ($num > 20 && $num < 30) {
            $res = "廿" . $dateHash[$num - 20];
        } else if ($num == 30) {
            $res = "三十";
        }
    }
    return $res;
}
function getWeeks($num){
    $weeks = [1=>'星期一',2=>'星期二',3=>'星期三',4=>'星期四',5=>'星期五',6=>'星期六',7=>'星期天'];
    return $weeks[$num];
}
function validationFilterIdCard($id_card)
{
    if (strlen($id_card) == 18) {
        return idcardChecksum18($id_card);
    } elseif ((strlen($id_card) == 15)) {
        $id_card = idcard15to18($id_card);
        return idcardChecksum18($id_card);
    } else {
        return false;
    }
}
// 计算身份证校验码，根据国家标准GB 11643-1999
function idcardVerifyNumber($idcard_base)
{
    if (strlen($idcard_base) != 17) {
        return false;
    }
    //加权因子
    $factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
    //校验码对应值
    $verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
    $checksum           = 0;
    for ($i = 0; $i < strlen($idcard_base); $i++) {
        $checksum += substr($idcard_base, $i, 1) * $factor[$i];
    }
    $mod           = $checksum % 11;
    $verify_number = $verify_number_list[$mod];
    return $verify_number;
}
// 将15位身份证升级到18位
function idcard15to18($idcard)
{
    if (strlen($idcard) != 15) {
        return false;
    } else {
        // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
        if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
            $idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
        } else {
            $idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
        }
    }
    $idcard = $idcard . idcardVerifyNumber($idcard);
    return $idcard;
}
// 18位身份证校验码有效性检查
function idcardChecksum18($idcard)
{
    if (strlen($idcard) != 18) {
        return false;
    }
    $idcard_base = substr($idcard, 0, 17);
    if (idcardVerifyNumber($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
        return false;
    } else {
        return true;
    }
}

function putLog($log_data)
{
    return file_put_contents('error_log.log', date('Y-m-d H:i:s') . ':' . $log_data . "\n", FILE_APPEND);
}

/**
 *封装函数，实现无限级分类功能
 *@param1 array  $data，  要遍历的二维数组
 *@param2 int    $pid，   默认的pid值
 *@param3 int    $level， 数据代表的层级
 *@return array  实现分类的二维数组
 */
function tree($data, $pid = 0, $level = 0)
{
    //为了让$tree的值不每次循环清零，把它设为静态变量
    static $tree = array();
    //遍历$data数组
    foreach ($data as $row) {
        //判断第一次循环获取$row['pid']=0的值，之后的循环为本级pid=上一级id
        if ($row['pid'] == $pid) {
            //几录当前的层级数
            $row['level'] = $level;
            //存储当前取得的值
            $tree[] = $row;
            //递归调用，
            $this->tree($data, $row['id'], $level + 1);
        }
    }
    // 返回实现分类的二维数组
    return $tree;
}
/**
 * PHP防止XSS攻击过滤函数
 * @param  string $val 需要过滤的数据
 * @return string      过滤后的数据
 */
function removeXSS($val)
{

    static $obj = null;

    if ($obj === null) {

        require '../HTMLPurifier/HTMLPurifier.includes.php';

        $obj = new HTMLPurifier();
    }

    return $obj->purify($val);
}
// 自动完成时候的注册时间处理函数
// $time 参数是表单提交的数据
function addTime($time)
{
    if ($time) {
        // 转换
        return strtotime($time);
    } else {
        return time();
    }
}
