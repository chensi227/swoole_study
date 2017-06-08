<?php

class TimerServer
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 4,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1 ,
        ));
        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Finish',array($this,'onFinish'));
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->start();
    }
    public function onStart( $serv ) {
        echo "开始\n";
    }

    public function onWorkerStart( $serv , $worker_id) {
        // 在Worker进程开启时绑定定时器
        echo "onWorkerStart\n";
        // 只有当worker_id为0时才添加定时器,避免重复添加
        if( $worker_id == 0 ) {
            swoole_timer_tick(1000,function($timer_id,$params){
                echo "这是一个定时任务\n";
                echo "param:{$params}\n";
            },"hello");
        }
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
        $serv->send($fd,"hello chensisi\n");
        swoole_timer_after(1000,function() use($serv,$fd){
            echo "Timer running\n";
            $serv->send($fd,"hello after\n");
        });     
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }

    public function onTick($timer_id, $params = null) {
        echo "Timer {$timer_id} running\n";
        echo "params {$params}\n";
    }

    public function onTask($serv,$task_id,$from_id,$data){
        echo "This Task {$task_id} from Worker {$from_id}\n";
        return 'Findish';
    }

    public function onFinish($serv,$task_id,$data)
    {
        echo "Tsak {$task_id} finish \n";
        echo "result:{$data} \n";
    }

}

new TimerServer();