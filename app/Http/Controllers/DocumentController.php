<?php

namespace App\Http\Controllers;

use App\Document;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function upload(Request $request) { //
        $data = $this->handleData($request);
        if (is_string($data)) {
            return $data;
        }
        $document = new Document($data);
        $result = $document->save();
        if ($result) {
            // 收录到本人的 上传 并获取下载权
            $uploader = User::query()->find($document->uploader);
            $uploader->addDownload($document->id);
            $uploader->addUpload($document->id);

            // 记录流水
            DB::table('uploads')->insert([
                'user_id' => $document->uploader,
                'document_file_name' => $document->filename
            ]);

            return msg(0, null);
        } else {
            unlink(storage_path() . '/document/' . $data['filename']); //模型插入数据库失败时需要删除已存储的文件
            return msg(4, __LINE__);
        }
    }

    public function documentInfo(Request $request) {
        $document = Document::query()->find($request->route('id'));
        if(!$document) {
            return msg(3, __LINE__);
        }
        $downloads = User::query()->find(session('id'))->download;
        $documents = json_decode($downloads, true)['data'];
        $buy = in_array($request->route('id'), $documents);

        return msg(0, array_merge($document->info(), array('buy' => $buy)));
    }

    public function buyDocument(Request $request) {
        $user = User::query()->find(session('id'));
        if(in_array($request->route('id') , json_decode($user->download))) {
            return msg(8, "已拥有下载权" . __LINE__);
        }

        $document = Document::query()->find($request->route('id'));
        $uploader = User::query()->find($document->uploader);

        if(!$user || !$document || !$uploader) {
            return msg(3, __LINE__);
        }

        if( !$user->earnScore(-$document->score) ) {
            return msg(7, '积分不足');
        }

        $user->addDownload($request->route('id'));
        $uploader->earnScore($document->score);

        //记录流水
        DB::table('scores')->insert([
            [
                'user_id' => $user->id,
                'spend' => $document->score,
                'way' => '获取文档',
                'time' => time()
            ],[
                'user_id' => $uploader->id,
                'spend' => $document->score,
                'way' => '文档被获取',
                'time' => time()
            ]
        ]);
        DB::table('buys')->insert([
            'user_id' => $user->id,
            'spend' => $document->score,
            'document_id' => $document->id,
            'time' => time()
        ]);

        return msg(0, '购买成功');
    }


}
