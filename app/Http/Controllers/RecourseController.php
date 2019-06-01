<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecourseController extends Controller
{
    private function handleData(Request $request = null) {
        $mod = array(
            'title' => '/^[\s\S]{0,300}$/',
            'description' => '/^[\s\S]{0,600}$/',
            'score' => '/^(1|)\d$/',
            'tag' => '/^\d$/',
            'urgent' => '/^1|0$/'
        );
        // TODO 处理求助数据 发布或更新
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
