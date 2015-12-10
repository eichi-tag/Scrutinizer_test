<?php
/**
 * RedisManagerクラス
 *
 *
 **/
class RedisManager{
    /**
     *
     * コンストラクタ
     *
     **/
    function __construct(){
        global $__config;

        $this->connect();
    }

    /**
     *
     * connect
     *
     **/
    function connect(){
        global $__config,$__logger,$__redis;

        if ($__config == null){
            return false;
        }

        try {
            if ( file_exists ( __CONF_PATH__ . "/env.txt") ){
                $env = "test";
            }else{
                $env = "prod";
            }
            $host_array = $__config['redis'][$env.'.new_host'];
            $prev_host_array = $__config['redis'][$env.'.prev_host'];
            $__redis = new RedisArray($host_array,array('previous'=>$prev_host_array,'connect_timeout'=>1));

        } catch (Exception $e) {
            $__logger->error("Redis Connect Error");
        }

    }

    /**
     *
     * Hash型更新
     *
     * Return Value : Bool
     *
     **/
    function hset ($redis_key , $redis_field , $expire = null ){
        global $__redis,$__config;
        if ( $redis_key == '' || !is_array($redis_field) || count($redis_field) == 0 ){
            return false;
        }

        $ret = $__redis->hMset($redis_key , $redis_field );
        
        if ( $ret ){
            $ttl = $__redis->ttl($redis_key);
            if ( $ttl == -1 ){
                // KeyのTTLが指定されていない
                if ( $expire == "" ){
                    $expire = $__config['redis']['expire'];
                }
                $ttl = $__redis->expire($redis_key,$expire);
            
            }elseif ( $ttl == -2 ){
                // keyが存在しない
                return -2;
            }else{
                // TTL取得
                if ( $expire != "" ){
                    $ttl = $__redis->expire($redis_key,$expire);
                }
            }
        }

        return $ret;
    }

    /**
     *
     * Hash型取得
     *
     * Return Value : Array
     *
     **/
    function hget ($redis_key , $redis_field ){
        global $__redis;
        if ( is_array( $redis_field ) ){
            $res = $__redis->hMget ( $redis_key , $redis_field );
        }else{
            $res = $__redis->hGet ( $redis_key , $redis_field );
        }
        return $res;
    }

    /**
     *
     * Hash型全ての情報を取得
     *
     * Return Value : Array
     *
     **/
    function hgetall ($redis_key ){
        global $__redis;
        if ( $redis_key == "" ){
            return false;
        }
        $res = $__redis->hGetAll( $redis_key );

        return $res;
    }

    /**
     *
     * Hash型増分指定の加算
     *
     **/
    function hincrby ($redis_key , $redis_field ){
        global $__redis;
        if ( is_array( $redis_field ) ){
            $res = array();
            foreach ($redis_field as $field_key => $field_val ){
                $ret = $__redis->hIncrBy ( $redis_key , $field_key , $field_val);
            }
        }else{
            return false;
        }

        return true;
    }

    /**
     *
     * Hash型パージ
     *
     **/
    function hdel ($redis_key , $redis_field = null){
        global $__redis;
        if ( $redis_field != "" ){
            $ret = $__redis->hdel($redis_key , $redis_field);
        }else{
            $ret = $__redis->del($redis_key);
        }
        return $ret;
    }
}

