<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller {
    //

    public function login(Request $request) {
        session(['login' => false, 'id' => null]);
        $mod = array(
            'stu_id' => '/^20[\d]{8,10}$/',
            'password' => '/^[^\s]{8,20}$/',
        );
        if (!$request->has(array_keys($mod))) {
            return msg(1, __LINE__);
        }
        $data = $request->only(array_keys($mod));
        if (!check($mod, $data)) {
            return msg(3, '数据格式错误' . __LINE__);
        };
        $user = User::query()->where('stu_id', $data['stu_id'])->first();

        if (!$user) { // 该用户未在数据库中 用户名错误 或 用户从未登录
            //利用三翼api确定用户账号密码是否正确
            $output = checkUser($data['stu_id'], $data['password']);

            if ($output['code'] == 0) {
                $user = new User([
                    'nickname' => '未命名的小朋友', //默认信息
                    'stu_id' => $data['stu_id'],
                    'password' => md5($data['password']),
                ]);
                $result = $user->save();

                if ($result) {
                    //直接使用上面的 $user 会导致没有id  这个对象新建的时候没有id save后才有的id 但是该id只是在数据库中 需要再次查找模型
                    $user = User::query()->where('stu_id', $data['stu_id'])->first();
                    session(['login' => true, 'id' => $user->id]);

                    return msg(0, $user->info());
                } else {
                    return msg(4, __LINE__);
                }

            }
        } else { //查询到该用户记录
            if ($user->password === md5($data['password'])) { //匹配数据库中的密码
                session(['login' => true, 'id' => $user->id]);
                return msg(0, $user->info());
            } else { //匹配失败 用户更改密码或者 用户名、密码错误
                $output = checkUser($data['stu_id'], $data['password']);
                if ($output['code'] == 0) {
                    $user->password = md5($data['password']);
                    $user->save();
                    session(['login' => true, 'id' => $user->id]);
                    return msg(0, $user->info());
                }
            }
        }

        return msg(2, __LINE__);
    }

    public function getUserInfo() {
        $user = User::query()->where('id', session('id'))->first();

        if ($user) {
            return msg(0, $user->info());
        } else {
            return msg(4, __LINE__);
        }
    }

    /**
     * 单独保存头像文件
     * @param Request $request 带有头像文件的请求
     * @return array|false|string
     */
    protected function saveAvatar(Request $request) {
        if (!$request->hasFile('avatar')) {
            return msg(3, '文件格式错误');
        }
        $savePath = public_path() . '/uploadfolder/avatar';

        // 如下 config('user.*') 值为 \app\config\user 中 键为*的元素的值
        $filename = session('id') . '.jpg';
        $request->file('avatar');
        $file_type = config('user.avatar_type');
        $file_limit = config('user.avatar_limit');
        $file_info = saveFile($request->file('document'),
            $file_limit, //文件大小限制
            $savePath,
            $file_type,
            false);
        if (is_string($file_info)) {
            return $file_info;
        }
        User::query()->where('id', session('id'))->update(['avatar' => $file_info['filename']]);
    }
}
