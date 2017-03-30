<?php

class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 4,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        $this->serv->on('Close', array($this, 'onClose'));

        $this->serv->start();
    }

    public function onStart( $serv ) {
        echo "启动\n";
    }

    public function onConnect( $serv, $fd, $from_id ) {
//        $serv->send( $fd, "Hello {$fd}!" );
        echo "客户端 {$fd} 连接\n";
    }

    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
        foreach($serv->connections as $client){
            if($fd !== $client){
                $serv->send($client,$data);
            }
        }
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "客户端 {$fd} 退出连接\n";
    }
}

$server = new Server();