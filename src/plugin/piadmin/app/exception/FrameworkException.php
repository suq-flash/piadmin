<?php
namespace plugin\piadmin\app\exception;

use Webman\Http\Request;
use Webman\Http\Response;
use support\exception\BusinessException;

/**
 * 框架内部异常
 */
class FrameworkException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
    }
}