<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'stu_id', 'nickname', 'upload', 'download', 'collection', 'avatar', 'score'
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
    public function info() {
        $info = array (
            'user_id' => $this->id,
            'stu_id' => $this->stu_id,
            'nickname' => $this->nickname,
            'email' => $this->email,
            'score' => $this->score,
            'avatar' => $this->avatar
        );
        return $info;
    }

    public function documents() {
        return $this->hasMany('App\Document', 'uploader', 'id');
    }
}
