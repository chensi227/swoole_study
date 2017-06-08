<?php

class Server
{
    private $serv;

    public function __construct() {
        $this->serv = new \swoole_server("0.0.0.0", 9501);
        $this->serv->set(array(
            'worker_num' => 8,
            'daemonize' => false,
            'max_request' => 10000,
            'dispatch_mode' => 2,
            'debug_mode'=> 1,
            'task_worker_num' => 8
        ));

        $this->serv->on('Start', array($this, 'onStart'));
        $this->serv->on('Connect', array($this, 'onConnect'));
        $this->serv->on('Receive', array($this, 'onReceive'));
        //bind callback
        $this->serv->on('Finish',array($this,'onFinish'));
        $this->serv->on('Task', array($this, 'onTask'));
        $this->serv->on('Close', array($this, 'onClose'));
        $this->serv->start();
    }

    //开始的调用
    public function onStart( $serv ) {
        echo "开始\n";
    }

    //连接的时候调用
    public function onConnect( $serv, $fd, $from_id ) {
        $serv->send( $fd, "Hello {$fd}!" );
    }

    /*
     *   OnConnect，建立连接，也就是电话被拨通的时候发生。
     *   OnReceive，收到消息，也就是服务听到客户说的话。
     *   OnClose，关闭连接，也就是客户\服务其中一方挂掉了电话时发生。
     *
     * */
    //接收到数据时回调此函数，发生在worker进程中
    //$fd客户端描述符
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
        $data = [
            'task'  =>  'task on',
            'params'    => $data,
            'fd'        =>  $fd,
        ];
        $serv->task(json_encode($data));
    }

    public function onTask($serv,$task_id,$from_id,$data){
        echo "This Task {$task_id} from Worker {$from_id}\n";
        echo "data:{$data} \n";

        $data = json_decode($data,true);
        var_dump($data);
        $serv->send($data['fd'],'hello task');
        return 'Findish';
    }

    public function onFinish($serv,$task_id,$data)
    {
        echo "Tsak {$task_id} finish \n";
        echo "result:{$data} \n";
    }

    public function onClose( $serv, $fd, $from_id ) {
        echo "Client {$fd} close connection\n";
    }
}

$server = new Server();
