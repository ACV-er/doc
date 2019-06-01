<?php

namespace App\Http\Controllers;

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
                if(!$result) {
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
        // TODO 提交帮助
    }

    public function update(Request $request) {
        $data = $this->handleData($request);
        if (is_string($data)) {
            return $data;
        }

        $result = Recourse::query()->find($request->route('id'))
            ->update($data);

        if($result) {
            return msg(0, __LINE__);
        } else {
            return msg(4, __LINE__);
        }
    }

    public function delete(Request $request) {
        // TODO 删除求助，(积分回收)
    }

    public function accept(Request $request) {
        // TODO 用户采纳回答
    }
}
