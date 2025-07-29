<?php

namespace plugin\piadmin\app\controller;

use support\Request;

class IndexController
{

    public function index()
    {
        // 切换多语言
        //locale('en');

        // 使用多语言
        return success([],trans(500));
    }

}
