<?php
//引入配置文件
include BASE_PATH . '/config/config.php';

//显示错误日志
if (DEBUG){
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

//引入路由文件
require BASE_PATH."/config/route.php";