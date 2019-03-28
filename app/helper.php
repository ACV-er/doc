<?php
    function check($mod, $data_array)
    { //$mod为数据数组键名对应数据的正则, $data_array为数据数组
        foreach ($data_array as $key => $value) { //$data_array的键名在$mod数组中必有对应  若无请检查调用时有无逻辑漏洞
            if (!preg_match($mod[$key], $value)) {
                return false; //此处数据有误
            }
        }

        return true;
    }

    function msg($code, $msg)
    {
        $status = array(
            0 => '成功',
            1 => '缺失参数',
            2 => '账号密码错误',
            3 => '错误访问',
            4 => '未知错误',
            5 => '其他错误',
            6 => '未登录'
        );

        $result = array(
            'code' => $code,
            'status' => $status[$code],
            'data' => $msg
        );

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
