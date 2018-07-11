<?php
namespace app\common\util;

class RedisLock {

    //锁的超时时间
    const TIMEOUT = 20;

    const SLEEP = 50000;//0.05S //1000000

    /**
     * Stores the expire time of the currently held lock
     * 当前锁的过期时间
     * @var int
     */
    protected static $expire;
    protected static $randomList = array();
    
    public static function getRedis()
    {    	
        return redis();
    }

    /**
     * Gets a lock or waits for it to become available
     * 获得锁，如果锁被占用，阻塞，直到获得锁或者超时
     *
     * 如果$timeout参数为0，则立即返回锁。
     * 
     * @param  string    $key        
     * @param  int        $timeout    Time to wait for the key (seconds)
     * @return boolean    成功，true；失败，false
     */
    public static function lock($key, $timeout = null){
        if(!$key)
        {
            return false;
        }
        return true;
 
        $start = time();

        $redis = self::getRedis();
        self::$expire = self::timeout();
		$randValue = time().mt_rand(1,999999);
        
        do{
        	$acquired = ($redis->set("Lock:{$key}", $randValue,array('nx','ex'=>(self::$expire)) ));
            if(!$acquired)
            {
                break;
            }

            if($timeout === 0) 
            {
                //如果超时时间为0，即为
                break;
            }
 
            usleep(self::SLEEP);

        } while(!is_numeric($timeout) || time() < $start + $timeout);
 
        if(!$acquired)
        {
            //超时
            return false;
        }
 		self::$randomList[$key] = $randValue;
        return true;
    }
 
    /**
     * Releases the lock
     * 释放锁
     * @param  mixed    $key    Item to lock
     * @throws LockException If the key is invalid
     */
    public static function release($key){
        if(!$key)
        {
            return false;
        }

        $redis = self::getRedis(); 
        // Only release the lock if it 's value == $random
        if(isset(self::$randomList[$key]) && self::$randomList[$key] != "" && self::$randomList[$key] == $redis->get("Lock:{$key}")) 
        {
            $redis->del("Lock:{$key}");
            unset(self::$randomList[$key]);
        }
    }
    
    public static function releaseAll(){
    	
    
    	$redis = self::getRedis();
    	foreach(self::$randomList as $key=>$random){
    		if($random == $redis->get("Lock:{$key}"))
    		{
    			$redis->del("Lock:{$key}");
    		}
    	}
    	self::$randomList = array();
    }
 
    /**
     * Generates an expire time based on the current time
     * @return int    timeout
     */
    protected static function timeout(){
        return (int) (self::TIMEOUT + 1);
    }
 
    
}

