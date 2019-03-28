<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    //

    private function chechUser($sid, $password)
    {
        $api_url = "https://api.sky31.com/edu-new/student_info.php";
        $api_url = $api_url . "?role=" . env('ROLE') . '&hash=' . env('HASH') . '&sid=' . $sid . '&password=' . $password;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    public function login(Request $request)
    {
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

        if (!$user) {
            // 该用户未在数据库中 用户名错误 或 用户从未登录
            //利用三翼api确定用户账号密码是否正确
            $output = $this->chechUser(urlencode($data['stu_id']), $data['password']);

            if ($output['code'] == 0) {
                $info = array(
                    'nickname' => '未命名的小朋友',
                    'stu_id' => $data['stu_id'],
                    'password' => md5($data['password']),
                );
                $user = new User($info);
                $result = $user->save();
                if ($result) {
                    session(['login' => true, 'id' => $user->id]);
                    return msg(0, $user->info());
                } else {
                    return msg(4, __LINE__);
                }

            } else {
                //失败
                return msg(2, __LINE__);
            }
        } else {
            if ($user->password === md5($data['password'])) {
                session(['login' => true, 'id' => $user->id]);
                return msg(0, $user->info());
            } else {
                $output = $this->chechUser(urlencode($data['stu_id']), $data['password']);
                if ($output['code'] == 0) {
                    $user->password = md5($data['password']);
                    $user->save();
                    session(['login' => true, 'id' => $user->id]);
                    return msg(0, $user->info());
                } else {
                    return msg(2, __LINE__);
                }
            }

        }

    }

    public function getUserInfo()
    {
        $user = User::query()->where('id', session('id'))->first();

        if ($user) {
            return msg(0, $user->info());
        } else {
            return msg(4, __LINE__);
        }
    }

    protected function saveAvatar(Request $request)
    {
        if (!$request->hasFile('avatar')) {
            return msg(3, '文件格式错误');
        }
        $file = $request->file('avatar');
        $allow_ext = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = $file->getClientOriginalExtension();
        if ($file->getSize() > 2048000) { //2M
            return msg(3, '文件大小' . __LINE__);
        }
        if (in_array($extension, $allow_ext)) {
            $savePath = public_path() . '/upload/avatar';
            $filename = session('id') . '.jpg';
            $file->move($savePath, $filename);
            User::query()->where('id', session('id'))->update(['avatar' => $filename]);
            return msg(0, '成功');
        } else {
            return msg(3, '文件格式错误'.__LINE__);
        }
    }
}
