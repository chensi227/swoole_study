<?php
/**
 * Created by PhpStorm.
 * User: Chensi
 * Date: 2017/3/28
 * Time: 22:50
 */
$socket = stream_socket_client("tcp://127.0.0.1:9501",$errno,$errstr,30);
function onRead()
{
    global $socket;
    $buffer = stream_socket_recvfrom($socket,1024);
    if(!$buffer){
        echo "server closed\n";
        swoole_event_del($socket);
    }
    echo "\nRECV: {$buffer}\n";
    fwrite(STDOUT,"你好,请输入消息:");
    
}
function onWrite()
{
    global $socket;
    echo "on write\n";
}
 
function onInput()
{
    global $socket;
    $msg = trim(fgets(STDIN));
    if($msg == 'exit'){
        swoole_event_exit();
        exit();
    }
    swoole_event_write($socket,$msg);
    fwrite(STDOUT,"您好,请输入消息:");
}

swoole_event_add($socket,'onRead','onWrite');
swoole_event_add(STDIN,'onInput');
fwrite(STDOUT,"请输入消息:");