<?php
/**
 * Created by JetBrains PhpStorm.
 * User: IBM
 * Date: 13-7-23
 * Time: 下午12:41
 * To change this template use File | Settings | File Templates.
 */

class RedisQueue extends CApplicationComponent{

    public $servers = array();
    private $_prefix = "queue_list_";

    //Yii::app()->queue->enqueue("test",array(1,2,3));
    public function enqueue($queue_name , $data , $server = "s1") {

        $redis = $this->getRedisConnection($server);
        return $redis->rPush($this->_prefix . $queue_name, json_encode($data));
    }


    /**
     * @param $queue_name
     * @param string $server
     * @param bool $fifo 先入先出？
     * @return bool|mixed
     */
    public function dequeue($queue_name , $server = "s1", $fifo = true) {

        $redis = $this->getRedisConnection($server);

        if($fifo) {
            $data = $redis->lPop($this->_prefix . $queue_name);
        } else {
            $data = $redis->rPop($this->_prefix . $queue_name);
        }


        if($data) {
            return json_decode($data,1);
        }

        return false;

    }


    public function getRedisConnection($server, $time_out = 0.1) {

        static $connection = array();
        if(!isset($this->servers[$server])) {
            throw new ERedisQueueException("redis queue server:$server not found");
        }

        $conf = explode(":", $this->servers[$server]);
        if(!isset($connection[$this->servers[$server]])) {
            $redis = new redis();
            $conn = $redis->connect($conf[0], $conf[1], $time_out);

            if($conn) {
                $connection[$this->servers[$server]] = $redis;
            } else {
                throw new ERedisQueueException("redis connect error");
            }
        }

        return $connection[$this->servers[$server]];
    }
}

class ERedisQueueException extends CException {}
