<?php

namespace App\Http\Controllers;

use App\Document;
use App\Recourse;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $data['presenter'] = session('id');

        return $data;
    }

    /**
     * 发布求助
     * @param Request $request
     * @return array|string
     */
    public function release(Request $request) {
        $data = $this->handleData($request);
        if (is_string($data)) {
            return $data;
        }

        // 默认没有提交的解答设置为空json（mysql json不能有默认值，在初始化时赋值）
        $data['solutions'] = '[]';

        $recourse = new Recourse($data);

        $presenter = User::query()->find($recourse->presenter);

        // 检查求助者积分数量是否够用
        if ($presenter->score >= $recourse->score) {
            DB::transaction(function () use ($presenter, $recourse) {
                // 扣除积分
                DB::table('users')->where('id', $presenter->id)
                    ->decrement('score', $recourse->score);

                //记录流水，加入事务处理
                DB::table('scores')->insert([
                    [
                        'user_id' => $presenter->id,
                        'spend' => -$recourse->score,
                        'way' => '发起请助',
                        'time' => date('Y-m-d H:i:s', time())
                    ]
                ]);
                $result = $recourse->save();
                if (!$result) {
                    DB::rollBack();
                }

                DB::commit();
                return msg(0, __LINE__);
            });
        } else {
            return msg(7, __LINE__);
        }

        return msg(4, __LINE__);
    }

    public function submit(Request $request) {
        // 先当做文件上传 默认分值为0分 （）
        $request->offsetSet('score', 0);

        // 先当做普通上传
        // FIXME 有空把这段优化一下
        $tmp = new DocumentController();
        $tmp = $tmp->__upload($request);
        if(json_decode($tmp, true)['code'] != 0) {
            return $tmp;
        }

        // TODO 提交帮助
    }

    /**
     * 更新求助内容
     * @param Request $request
     * @return array|string
     */
    public function update(Request $request) {
        // 若已解决，无法更改
        $recourse = Recourse::query()->find($request->route('id'));
        if ($recourse->helper != -1) {
            return msg(3, __LINE__);
        }

        $data = $this->handleData($request);
        if (is_string($data)) {
            return $data;
        }

        $result = $recourse->update($data);
        if ($result) {
            return msg(0, __LINE__);
        } else {
            return msg(4, __LINE__);
        }
    }

    /**
     * 删除求助
     * @param Request $request
     * @return string
     */
    public function delete(Request $request) {
        // 若已解决，无法删除
        $recourse = Recourse::query()->find($request->route('id'));
        if ($recourse->helper != -1) {
            return msg(3, __LINE__);
        }

        $result = Recourse::destroy($request->route('id'));

        if ($result) {
            return msg(0, __LINE__);
        } else {
            return msg(4, __LINE__);
        }
    }

    public function accept(Request $request) {
        // TODO 用户采纳帮助
        // solutions
        $recourse = Recourse::query()->find($request->route('id'));
        $solutions = json_decode($recourse->solutions);
        if (in_array($request->route('document_id'), $solutions)) {
            $recourse->solution = $request->route('document_id');
            $recourse->helper = Document::query()->find($request->route('document_id'))
                                                 ->get('uploader');
        }
    }
}
