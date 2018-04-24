<?php
use \NoahBuscher\Macaw\Macaw as Route;

Route::get('/', '\app\controller\index@index');

Route::get('/admin', '\app\controller\crawl@index');
Route::get('/index','\app\controller\index@index');

Route::dispatch();  //不可删除,否则无法路由