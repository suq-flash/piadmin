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

if (!function_exists('isNotBlank')) {
    /**
     * 判非空
     * @param $var
     * @return bool
     */
    function isNotBlank($var): bool
    {
        return $var !== '' && $var !== null && $var !== false && $var !== [];
    }
}

if (!function_exists('isBlank')) {
    /**
     * 判空
     * @param $var
     * @return bool
     */
    function isBlank($var): bool
    {
        return !isNotBlank($var);
    }
}

if (!function_exists('get_column_for_key_array')) {
    function get_column_for_key_array(array $data, string $key = 'id'): array
    {
        $item = [];
        foreach ($data as $val) {
            $keys = explode(',',$key);
            $tmpKey = '';
            foreach ($keys as $kv){
                if (! array_key_exists($kv, $val)) {
                    break;
                }
                $tmpKey .= $val[$kv] ?? null;
            }
            $item[$tmpKey] = $val;

        }
        return $item;
    }
}

