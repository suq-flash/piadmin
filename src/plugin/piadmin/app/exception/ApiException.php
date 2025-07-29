<?php
// +----------------------------------------------------------------------
// | saiadmin [ saiadmin快速开发框架 ]
// +----------------------------------------------------------------------
// | Author: sai <1430792918@qq.com>
// +----------------------------------------------------------------------
namespace plugin\piadmin\app\exception;

use Webman\Http\Request;
use Webman\Http\Response;
use support\exception\BusinessException;

/**
 * 常规异常
 */
class ApiException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
    }
}