<?php
namespace plugin\piadmin\app\middleware;

use Tinywan\Jwt\JwtToken;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class CheckLogin implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        // 获取不需要登录方法
        $controller = new \ReflectionClass($request->controller);
        $noNeedLogin = $controller->getDefaultProperties()['noNeedLogin'] ?? [];

        // 访问的方法需要登录
        if (!in_array($request->action, $noNeedLogin)) {
            try {
                $token = JwtToken::getExtend();
//                $request->setHeader('check_login', true);
//                $request->setHeader('check_admin', $token);
            } catch (\Throwable $e) {
                return error([],'您的登录凭证错误或者已过期，请重新登录',[],403);
            }
        }

        return $handler($request);
    }
}
