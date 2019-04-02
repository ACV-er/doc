<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model {
    //
    protected $fillable = [
        'name', 'type', 'tag', 'uploader', 'title', 'downloads', 'description', 'score', 'size', 'filename', 'md5'
    ];

    public function info() {
        $info = array(
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'tag' => $this->tag,
            'uploader' => $this->uploader,
            'score' => $this->score,
            'downloads' => $this->downloads,
            'description' => $this->description,
            'title' => $this->title,
        );
        return $info;
    }
}
