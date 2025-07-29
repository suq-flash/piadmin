<?php
namespace plugin\piadmin\exception;

use Throwable;
use Webman\Exception\ExceptionHandlerInterface;
use Webman\Http\Request;
use Webman\Http\Response;

/**
 * 默认异常处理类
 */
class ExceptionHandler implements ExceptionHandlerInterface {

    private $_debug;

    public function __construct()
    {
        $this->_debug = config('plugin.piadmin.app.debug');
    }

    //日志记录
    public function report(Throwable $exception)
    {
        //TODO 记录日志
    }

    public function render(Request $request, Throwable $exception): Response
    {
        $code = $exception->getCode();

        $json = ['code' => $code ? $code : 500, 'msg' => $exception->getMessage(),'data'=>[]];
        //$this->_debug && $json['traces'] = (string)$exception;
        if($this->_debug){
            $json['request_url'] = $request->method() . ' ' . $request->uri();
            $json['timestamp'] = date('Y-m-d H:i:s');
            $json['client_ip'] = $request->getRealIp();
            $json['request_param'] = $request->all();
            $json['exception_handle'] = get_class($exception);
            $json['exception_info'] = [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => explode("\n", $exception->getTraceAsString())
            ];
        }

        return new Response(200, ['Content-Type' => 'application/json'],
            \json_encode($json, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

    }

}