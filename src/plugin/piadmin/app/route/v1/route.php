<?php
use Webman\Route;

Route::group('/piadmin/v1',function (){

    Route::any('/login',['plugin\piadmin\app\controller\IndexController','index']);

});