<?php
/**
 * Here is your custom functions.
 */
if (!function_exists('success')) {
    /**
     * @param $data
     * @param $msg
     * @param $other_result
     * @param $code
     * @return \support\Response
     */
    function success($data = '', $msg = 'success', $other_result = [], $code = 0): \support\Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];

        $result = array_merge($result, $other_result);
        return json($result);
    }
}

if (!function_exists('error')) {
    /**
     * @param $data
     * @param $msg
     * @param $other_result
     * @param $code
     * @return \support\Response
     */
    function error($data = '', $msg = 'success', $other_result = [], $code = 422): \support\Response
    {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];

        $result = array_merge($result, $other_result);
        return json($result);
    }
}

