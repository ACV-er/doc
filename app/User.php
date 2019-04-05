<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 * @property int $id 用户标识
 * @property int $stu_id 学号
 * @property int $score 积分
 * @property string $nickname
 * @property string $upload 上传列表
 * @property string $download 购买列表（可下载列表）
 * @property string $collection 收藏列表
 * @property string $avatar 头像文件名称
 * @property array info() 个人信息
 * @property void addDownload(int $document_id)
 * @property void addUpload(int $document_id)
 * @property void delUpload(int $document_id)
 * @property void addCollection(int $document_id)
 * @property void delCollection(int $document_id)
 * @property bool earnScore(int $num)
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'password', 'stu_id', 'nickname', 'upload', 'download', 'collection', 'avatar', 'score'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return array
     */
    public function info() {
        $info = array (
            'user_id' => $this->id,
            'stu_id' => $this->stu_id,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'score' => $this->score,
            'avatar' => $this->avatar,
            'downloads' => count(json_decode($this->download, true)),
            'collections' => count(json_decode($this->collection, true))
        );
        return $info;
    }

    /**向 下载 收藏 上传 数组中添加元素
     * @param $key
     * @param $document_id
     */
    private function addDocument($key, int $document_id) {
        $new = json_decode($this->$key, true);
        if(in_array($document_id, $new)) {
            return; //有就算了 不影响
        }

        array_push($new, $document_id);

        $this->$key = json_encode($new);
        $this->save();
    }

    private function delDocument($key, int $document_id) {
        $new = json_decode($this->$key, true);
        if(!in_array($document_id, $new)) {
            return; //没有就算了 不影响
        }

        $new = array_values(array_diff($new, [$document_id])); //array_diff 会把索引数组转为关联数组

        $this->$key = json_encode($new);
        $this->save();
    }

    /**积分变化
     * @param int $num 变化的值，消耗即为负
     * @return bool 积分不够返回false
     */
    public function earnScore(int $num) {
        if($this->score + $num < 0) {
            return false;
        }

        $this->score += $num;
        $this->save();

        return true;
    }

    public function addDownload(int $document_id) {
        $this->addDocument('download', $document_id);
    }
    //下载(购买)后不可删除

    public function addUpload(int $document_id) {
        $this->addDocument('upload', $document_id);
    }

    public function delUpload(int $document_id) {
        $this->delDocument('upload', $document_id);
    }

    public function addCollection(int $document_id) {
        $this->addDocument('collection', $document_id);
    }

    public function delCollection(int $document_id) {
        $this->delDocument('collection', $document_id);
    }

    public function documents() {
        return $this->hasMany('App\Document', 'uploader', 'id');
    }
}
