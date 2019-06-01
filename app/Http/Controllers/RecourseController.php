<?php

namespace App\Http\Controllers;

use App\Recourse;
use Illuminate\Http\Request;

class RecourseController extends Controller {
    private function handleData(Request $request = null) {
        $mod = array(
            'title' => '/^[\s\S]{0,300}$/',
            'context' => '/^[\s\S]{0,600}$/',
            'score' => '/^\d+$/',
            'tag' => '/^\d$/',
            'urgent' => '/^1|0$/'
        );
        // 检查数据是否完整
        if (!$request->has(array_keys($mod))) {
            return msg(1, __LINE__);
        }

        $data = $request->only(array_keys($mod));
        if (!check($mod, $data)) { // 检查数据是否符合 $mod 中对应的正则
            return msg(3, '数据格式错误' . __LINE__);
        }

        return $data;

    }

    public function release(Request $request) {
        // TODO 发布求助,(此处扣除积分)
    }

    public function submit(Request $request) {
        // TODO 提交帮助
    }

    public function update(Request $request) {
        //TODO 更新求助
    }

    public function delete(Request $request) {
        // TODO 删除求助，(积分回收)
    }

    public function accept(Request $request) {
        // TODO 用户采纳回答
    }
}
