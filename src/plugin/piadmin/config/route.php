<?php

use Webman\Route;

// 引入v1版本路由
require_once config('plugin.piadmin.piadmin.path').'/app/route/v1/route.php';


// 处理404路由
Route::fallback(function(){
    return error([],'Route not found');
});