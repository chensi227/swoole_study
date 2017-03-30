<?php
/**
 * Created by PhpStorm.
 * User: Chensi
 * Date: 2017/3/11
 * Time: 23:20
 */
$http = new swoole_http_server("0.0.0.0", 9501);
$http->on('request', function (swoole_http_request $request,swoole_http_response $response) {
//    print_r($request);
//    $response->status(404);
//    $request->end('404 not found');
    //向客户端发送请求
//    $response->end('hello swoole');
    $pathinfo = $request->server['path_info'];
    $filename = __DIR__.$pathinfo;
    if(is_file($filename)){
        //如果是php文件
        $ext = pathinfo($request->server['path_info'],PATHINFO_EXTENSION);
        if($ext == 'php'){
            //动态请求
            ob_start();
            include $filename;
            $content = ob_get_contents();
            ob_end_clean();
            $response->end($content);
        }else{
            //静态处理
            $content = file_get_contents($filename);
            $response->end($content);
        }
    }else{
        $response->status(404);
        $response->end('404 not found');
    }
});
$http->start();