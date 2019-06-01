<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recourse extends Model {
    protected $fillable = [
        'presenter', 'tag', 'context', 'helper', 'title', 'solution', 'solutions', 'score', 'urgent'
    ];
}
