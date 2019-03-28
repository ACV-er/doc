<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    //
    protected $fillable = [
        'name', 'type', 'tag', 'uploader', 'title', 'downloads', 'description', 'score', 'filename'
    ];
}
