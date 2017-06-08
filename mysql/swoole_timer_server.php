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
        $this->serv->on('Close', array($this, 'onClose'));
//        $this->serv->on('Timer', array($this, 'onTimer'));
        $this->serv->start();
	}

    public function onStart( $serv ) {
        echo "开始\n";
    }

	public function onWorkerStart( $serv , $worker_id) {
		// 在Worker进程开启时绑定定时器
        echo "onWorkerStart\n";
        // 只有当worker_id为0时才添加定时器,避免重复添加
        /*if( $worker_id == 0 ) {
        //addtimer  已经移除
        	$serv->addtimer(10000);
	        $serv->addtimer(5000);
            $serv->addtimer(1000);
        }*/
        if($worker_id == 0){
            swoole_timer_tick(1000,function($timer_id,$params){
                echo "Timer running\n";
                echo "recv:{$params}\n";
            },"hello");
        }
    }

    public function onConnect( $serv, $fd, $from_id ) {
        echo "Client {$fd} connect\n";
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }

    //已经移除
    /*public function onTimer($serv, $interval) {
    	switch( $interval ) {
    		case 5000: {	//
    			echo "Do Thing A at interval 500\n";
    			break;
    		}
    		case 1000:{
    			echo "Do Thing B at interval 1000\n";
    			break;
    		}
    		case 10000:{
    			echo "Do Thing C at interval 100\n";
    			break;
    		}
    	}
    }*/
}

new TimerServer();