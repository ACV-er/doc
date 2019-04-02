<?php

namespace App\Http\Controllers;

use App\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller {
    /**
     * 上传和更新进行统一数据处理
     * @param Request $request 前端请求
     * @return array|string $data 处理之后的数据 成功返回数组
     */
    private function handleData(Request $request = null) {
        $mod = array(
            'title' => '/^[\s\S]{0,300}$/',
            'description' => '/^[\s\S]{0,600}$/',
            'score' => '/^(1|)\d$/',
            'tag' => '/^\d$/'
        );

        // 检查数据是否完整
        if (!$request->hasFile('document') || !$request->has(array_keys($mod))) {
            return msg(1, __LINE__);
        }

        $data = $request->only(array_keys($mod));
        if (!check($mod, $data)) { // 检查数据是否符合 $mod 中对应的正则
            return msg(3, '数据格式错误' . __LINE__);
        }

        // 如下 config('user.*') 值为 \app\config\user 中 键为*的元素的值
        $file_limit = config('user.document_limit');
        $file_type = config('user.document_type');

        $filename = time().rand(0, 1000);
        $file_info = saveFile($request->file('document'),
            $file_limit, //文件大小限制
            storage_path() . '/document',
            $file_type,
            false,
            $filename);
        if (is_string($file_info)) {
            return $file_info;
        }
        $data = array_merge($data, $file_info);
        return $data;
    }

    /**
     * 上传文件
     * @param Request $request 包含文件 及其信息 具体见接口文档
     * @return array|false|string
     */
    public function upload(Request $request) {
        $data = $this->handleData($request);
        if (is_string($data)) {
            return $data;
        }
        $document = new Document($data);
        $result = $document->save();
        if ($result) {
            return msg(0, null);
        } else {
            unlink(storage_path() . '/document/' . $data['filename']); //模型插入数据库失败时需要删除已存储的文件
            return msg(4, __LINE__);
        }
    }
}
