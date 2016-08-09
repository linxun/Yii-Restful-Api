<?php
/**
 * Created by JetBrains PhpStorm.
 * User: linxun
 * Date: 13-7-23
 * Time: 上午11:35
 * To change this template use File | Settings | File Templates.
 */

abstract class QueueCommand extends CConsoleCommand {

    protected $queueName = null;
    protected $queueServer = "s1";
    protected $fifo = true;
    protected $maxTask = 10000;

    protected abstract function handle($data) ;

    public function actionStart() {

        try {

            for($i = 1; $i<= $this->maxTask; ++$i) {
                $data = Yii::app()->queue->dequeue($this->queueName, $this->queueServer, $this->fifo);
                if($data) {
                    $this->handle($data);
                } else {
                    $this->log("task cleared" , false);
                    return true;
                }
            }
            $this->log("done {$this->maxTask} tasks");

        } catch ( Exception $e) {
            $this->errorLog($e->getMessage(), 0);
        }
    }

    protected function errorLog($str, $error_level = 1) {
        $level = ($error_level) ? "[warning]" :"[fatal error]";
        $this->log($level . $str);
    }

    protected function log($str, $fileLog = true) {
        global $argv;
        $date = '['.date("Y-m-d H:i:s")."]";

        if( $fileLog && !empty($argv[3]) ) {
            $logFileName = $argv[3].$argv[1]."-".date("Y-m-d").".log";
            error_log("$date$str\n", 3, $logFileName);
        }
        echo "{$date}[{$argv[1]}]$str\n";
    }
}