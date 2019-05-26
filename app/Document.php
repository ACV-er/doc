<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


/**
 * Class Document
 * @property  int $id
 * @property  int $name
 * @property  int $type
 * @property  int $tag
 * @property  int $uploader
 * @property  string $uploader_nickname
 * @property  int $score
 * @property  int $downloads
 * @property  string $description
 * @property  string $title
 * @property  string $page
 * @property  string $created_at
 * @property array info() 文档信息
 * @package App
 */
class Document extends Model {
    //
    protected $fillable = [
        'name', 'type', 'tag', 'uploader', 'uploader_nickname', 'title', 'downloads', 'description', 'score', 'size', 'filename', 'md5'
    ];

    public function info() {
        $info = array(
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'tag' => $this->tag,
            'uploader' => $this->uploader,
            'uploader_nickname' => $this->uploader_nickname,
            'score' => $this->score,
            'downloads' => $this->downloads,
            'description' => $this->description,
            'title' => $this->title,
            'page' => $this->page,
            'created_at' => date_format($this->created_at, 'Y-m-d H:i:s'),
        );
        return $info;
    }


}
