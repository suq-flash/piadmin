<?php

namespace plugin\piadmin\app\controller;

use support\Request;

class IndexController
{

    // 无需登录方法
    protected $noNeedLogin = ['index'];

    public function index()
    {
        // 切换多语言
        //locale('en');

        // 使用多语言
        return config('plugin.piadmin.piadmin.path');
        return success([],trans(500));
    }

}
