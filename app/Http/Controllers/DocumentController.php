<?php

namespace App\Http\Controllers;

use App\Document;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
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
        if (
            !($request->hasFile('document') || $request->routeIs('updateDocumentInfo'))
            || !$request->has(array_keys($mod))
        ) {
            return msg(1, __LINE__);
        }

        $data = $request->only(array_keys($mod));
        if (!check($mod, $data)) { // 检查数据是否符合 $mod 中对应的正则
            return msg(3, '数据格式错误' . __LINE__);
        }

        $file_info = array();
        if ($request->hasFile('document')) {
            // 如下 config('user.*') 值为 \app\config\user 中 键为*的元素的值
            $file_limit = config('user.document_limit');
            $file_type = config('user.document_type');

            $filename = time() . rand(0, 1000);
            $file_info = saveFile($request->file('document'),
                $file_limit, //文件大小限制
                storage_path() . '/document',
                $file_type,
                false,
                $filename);
            if (is_string($file_info)) {
                return $file_info;
            }
        }

        // 虽然此前 可能该文件并没有存入数据库 但是下面的脚本运行需要很久（3-5秒甚至更久）所以可以确保在该脚本访问服务器之前 服务器内有目标数据
        // 如果因为特殊原因 服务运行很慢 则该脚本可能去数据库内访问不存在的数据 导致不可预览

        exec("php7 " . base_path() . "/toJpg.php " . env('DB_DATABASE') . " " . env('DB_USERNAME') . " "
            . env('DB_PASSWORD') . " " . storage_path() . "/document/" . $file_info['filename'] . " > /dev/null &");

        $data = array_merge($data, $file_info);
        return $data;
    }

    /**
     * 上传文件
     * @param Request $request 包含文件 及其信息 具体见接口文档
     * @return string
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

            return msg(0, __LINE__);
        } else {
            unlink(storage_path() . '/document/' . $data['filename']); //模型插入数据库失败时需要删除已存储的文件
            return msg(4, __LINE__);
        }
    }

    /**获取文档信息
     * @param Request $request
     * @return string
     */
    public function documentInfo(Request $request) {
        $document = Document::query()->find($request->route('id'));
        if (!$document) {
            return msg(10, '目标不存在，或已删除' . __LINE__);
        }
        $downloads = User::query()->find(session('id'))->download;
        $documents = json_decode($downloads, true);
        $buy = in_array($request->route('id'), $documents);

        return msg(0, array_merge($document->info(), array('buy' => $buy)));
    }

    /**更新文档信息
     * @param Request $request
     * @return string
     */
    public function updateDocumentInfo(Request $request) {
        $data = $this->handleData($request);
        if (is_string($data)) {
            return $data;
        }

        $document = Document::query()->find($request->route('id'));
        $result = $document->update($data);

        if ($result) {
            return msg(0, __LINE__);
        } else {
            return msg(4, "BUG！" . __LINE__);
        }
    }

    /**更新文档文件 （不删除原来的文件，记录在流水中，备用）
     * @param Request $request
     * @return string
     */
    public function updateDocumentFile(Request $request) {
        if (!$request->hasFile('document')) {
            return msg(3, "文件不存在" . __LINE__);
        }

        // 如下 config('user.*') 值为 \app\config\user 中 键为*的元素的值
        $file_limit = config('user.document_limit');
        $file_type = config('user.document_type');

        $filename = time() . rand(0, 1000);
        $file_info = saveFile($request->file('document'),
            $file_limit, //文件大小限制
            storage_path() . '/document',
            $file_type,
            false,
            $filename);
        if (is_string($file_info)) {
            return $file_info;
        }


        $result = Document::query()->find($request->route('id'))->update($file_info);
        if ($result) {
            return msg(0, __LINE__);
        } else {
            return msg(3, __LINE__);
        }
    }

    /**删除发布
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function delDocument(Request $request) {
        $user = User::query()->find(session('id'));
        $result = Document::destroy($request->route('id'));

        $user->delUpload($request->route('id'));

        if ($result === 1) {
            return msg(0, __LINE__);
        } else {
            return msg(4, __LINE__);
        }
    }

    /**购买文档
     * @param Request $request
     * @return string
     */
    public function buyDocument(Request $request) {
        $user = User::query()->find(session('id'));
        if (in_array($request->route('id'), json_decode($user->download))) {
            return msg(8, "已拥有下载权" . __LINE__);
        }

        $document = Document::query()->find($request->route('id'));
        $uploader = User::query()->find($document->uploader);

        if (!$user || !$document || !$uploader) {
            return msg(3, __LINE__);
        }

        if (!$user->earnScore(-$document->score)) {
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
                'time' => date('Y-m-d H:i:s', time())
            ], [
                'user_id' => $uploader->id,
                'spend' => $document->score,
                'way' => '文档被获取',
                'time' => date('Y-m-d H:i:s', time())
            ]
        ]);
        DB::table('buys')->insert([
            'user_id' => $user->id,
            'spend' => $document->score,
            'document_id' => $document->id,
            'time' => date('Y-m-d H:i:s', time())
        ]);

        return msg(0, '购买成功');
    }

    /**文档下载
     * @param Request $request
     * @return string|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadDocument(Request $request) {
        $user = User::query()->find(session('id'));
        $document = Document::query()->find($request->route('id'));

        if (!$document) {
            return response(msg(10, "目标不存在，或已删除" . __LINE__), 200);
        }
        if (!in_array($request->route('id'), json_decode($user->download))) {
            return msg(9, "没有下载权" . __LINE__);
        }

        // 增加下载量
        $document->downloads += 1;
        $document->save();

        $file = storage_path() . '/document' . "/" . $document->filename;

        return Response::download($file, $document->name);
    }

    /**最新的十个文档
     * @param Request $request
     * @return string
     */
    public function newUpload(Request $request) {
        $offset = $request->route('page') * 10 - 10;
        $documentList = DB::table('documents')->orderBy('updated_at', 'desc')
            ->offset($offset)->limit(10)
            ->get(config('user.document_public_info'))->toArray();

        return msg(0, $documentList);
    }

    /**下载量最高的十个文档
     * @param Request $request
     * @return string
     */
    public function sortUpload(Request $request) {
        $offset = $request->route('page') * 10 - 10;
        $documentList = DB::table('documents')->orderBy('downloads', 'desc')
            ->offset($offset)->limit(10)
            ->get(config('user.document_public_info'))->toArray();

        return msg(0, $documentList);
    }

    /** 最多五个关键字
     * @param Request $request
     * @return string
     */
    public function search(Request $request) {
        $offset = $request->route('page') * 10 - 10;

        $param = ['tag', 'type', 'keyword'];
        if (!$request->has($param)) {
            return msg(1, __LINE__);
        }
        $data = $request->only($param);

        // 取出参数, 均为数组
        foreach ($param as $item) {
            $data[$item] = json_decode($data[$item], true);
            if (!is_array($data[$item])) {
                return msg(3, __LINE__);
            }
        }

        // 构造 %关键字% 格式
        //  该形式 %?% ?无法被解析为占位符
        $keyword = $data['keyword'];
        if (count($keyword) > 5) {
            return msg(3, __LINE__);
        }
        for ($i = 0; $i < count($keyword); $i++) {
            $keyword[$i] = "%" . $keyword[$i] . "%";
        }

        // 关键词不够则使用通配符 %_%
        for ($i = count($keyword); $i < 5; $i++) {
            $keyword[$i] = '%_%';
        }

        // 关键词搜索 看代码
        $result = Document::query()->whereIn('tag', $data['tag'])
            ->whereIn('type', $data['type'])
            ->whereRaw(
                "concat(`title`,`description`,`name`) like ? AND " .
                "concat(`title`,`description`,`name`) like ? AND " .
                "concat(`title`,`description`,`name`) like ? AND " .
                "concat(`title`,`description`,`name`) like ? AND " .
                "concat(`title`,`description`,`name`) like ?",
                $keyword)
            ->offset($offset)->limit(10)
            ->get(config('user.document_public_info'))->toArray();

        if ($result) {
            return msg(0, $result);
        } else {
            return msg(4, __LINE__);
        }
    }

    /**
     * @param Request $request
     * @return false|string string可能为图片内容
     */
    public function getJpg(Request $request) {
        $document = Document::query()->find($request->route('id'));
        if(!$document || $request->route('page') > $document->page) {
            return response(msg(10, "目标不存在，或已删除" . __LINE__), 200);
        }

        $path = public_path()."/storage/view/".preg_split("/\./", $document->filename)[0];
        $page = $request->route('page') - 1;
        $jpg = "$path/1-$page.jpg";

        header("Content-type: image/jpeg");

        return file_get_contents($jpg);
    }
}
