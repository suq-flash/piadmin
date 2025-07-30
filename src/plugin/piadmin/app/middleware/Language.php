<?php
namespace plugin\piadmin\app\middleware;

use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

/**
 * 多语言处理中间件
 */
class Language implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {

        $lang = $request->header('PIADMIN_LANG','zh_CN');

        $fallback_locale = config('plugin.piadmin.translation.fallback_locale');

        if(in_array($lang,$fallback_locale)){
            locale($lang);
        }

        return $handler($request);
    }
}
