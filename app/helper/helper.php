<?php

    use Symfony\Component\HttpFoundation\File\UploadedFile;
    use App\User;

    /**
     * 检测数据是否符合正则
     * @param array $mod 数据名=》正则 限制数组
     * @param array $data_array 数据数组
     * @return bool
     */
    function check(array $mod, array $data_array) { //$mod为数据数组键名对应数据的正则, $data_array为数据数组
        foreach ($data_array as $key => $value) { //$data_array的键名在$mod数组中必有对应  若无请检查调用时有无逻辑漏洞
            if (!preg_match($mod[$key], $value)) {
                if (env('APP_DEBUG')) {
                    echo "mod: " . $mod[$key] . "</br>value: " . $value . "</br>";
                }
                return false; //数据有误
            }
        }

        return true;
    }

    /**
     * 利用三翼借接口验证用户名密码
     * @param $sid
     * @param $password
     * @return mixed
     */
    function checkUser($sid, $password) { //登录验证
        $api_url = "https://api.sky31.com/edu-new/student_info.php";
        $api_url = $api_url . "?role=" . env('ROLE') . '&hash=' . env('HASH') . '&sid=' . $sid . '&password=' . urlencode($password);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return json_decode($output, true);
    }

    /**
     * 设置返回值
     * @param $code
     * @param $msg
     * @return false|string
     */
    function msg($code, $msg) {
        $status = array(
            0 => '成功',
            1 => '缺失参数',
            2 => '账号密码错误',
            3 => '错误访问',
            4 => '未知错误',
            5 => '其他错误',
            6 => '未登录',
            7 => '积分不足',
            8 => '重复购买',
            9 => '未购买'
        );

        $result = array(
            'code' => $code,
            'status' => $status[$code],
            'data' => $msg
        );

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    /** 图片压缩 用于头像 生成缩略图
     * @param $src_img
     */
    function compress($src_img) {
        $dst_w = 900;
        $dst_h = 600;
        list($src_w, $src_h) = getimagesize($src_img);  // 获取原图尺寸

        $dst_scale = $dst_h / $dst_w; //目标图像长宽比
        $src_scale = $src_h / $src_w; // 原图长宽比

        if ($src_scale >= $dst_scale) {  // 过高
            $w = intval($src_w);
            $h = intval($dst_scale * $w);

            $x = 0;
            $y = ($src_h - $h) / 3;
        } else { // 过宽
            $h = intval($src_h);
            $w = intval($h / $dst_scale);

            $x = ($src_w - $w) / 2;
            $y = 0;
        }

        // 剪裁
        $type = exif_imagetype($src_img);

        switch ($type) {
            case IMAGETYPE_JPEG :
                $source = imagecreatefromjpeg($src_img);
                break;
            case IMAGETYPE_PNG :
                $source = imagecreatefrompng($src_img);
                break;
            case IMAGETYPE_GIF :
                $source = imagecreatefromgif($src_img);
                break;
            default:
                $source = imagecreatefromjpeg($src_img);
                break;
        }
        $croped = imagecreatetruecolor($w, $h);
        imagecopy($croped, $source, 0, 0, $x, $y, $src_w, $src_h);

        // 缩放
        $scale = $dst_w / $w;
        $target = imagecreatetruecolor($dst_w, $dst_h);
        $final_w = intval($w * $scale);
        $final_h = intval($h * $scale);
        imagecopyresampled($target, $croped, 0, 0, 0, 0, $final_w, $final_h, $w, $h);

        // 保存
        unlink($src_img);
        switch ($type) {
            case IMAGETYPE_JPEG :
                imagejpeg($target, $src_img); // 存储图像
                break;
            case IMAGETYPE_PNG :
                imagepng($target, $src_img);
                break;
            case IMAGETYPE_GIF :
                imagegif($target, $src_img);
                break;
            default:
                imagejpeg($target, $src_img);
                break;
        }
        imagedestroy($target);
    }

    /**
     * 限制大小 文件后缀 的文件保存
     * @param UploadedFile|null $file 从请求中取出的文件
     * @param int $file_limit
     * @param string $savePath
     * @param array $type 支持的后缀的数组
     * @param bool $compress 是否启用图片压缩
     * @param string $filename 文件名
     * @return array|string 失败时放回msg 成功时返回信息数组
     */

    function saveFile(UploadedFile $file = null, int $file_limit = 2048000, string $savePath = "", array $type = [], bool $compress = false, string $filename = "") {
        $size = $file->getSize();
        if ($size > $file_limit) { //2M
            return msg(3, '文件大小' . __LINE__);
        }

        $allow_ext = array_keys($type);
        $extension = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();

        $filename = $filename . "." . $extension;
        if (in_array($extension, $allow_ext)) {
            $file->move($savePath, $filename);
            $compress ? compress($savePath . '/' . $filename) : 0;
            $type = $type[$extension];
            $data = array(
                'size' => $size,
                'name' => $name,
                'filename' => $filename,
                'type' => $type,
                'uploader' => session('id'),
                'uploader_nickname' => User::query()->find(session('id'))->nickname,
                'md5' => md5_file($savePath . '/' . $filename),
            );

            return $data;
        } else {
            return msg(3, '格式错误');
        }
    }
