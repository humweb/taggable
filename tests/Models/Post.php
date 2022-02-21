<?php

namespace Humweb\Taggable\Tests\Models;

use Humweb\Taggable\Models\Taggable;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use Taggable;

    protected $guarded = [];
    public $timestamps = false;
}
