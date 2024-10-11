<?php

namespace Pharaonic\Laravel\Translatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Sluggable\Sluggable;

class PostTranslation extends Model
{
    use Sluggable;  // Package : pharaonic/laravel-sluggable

    /**
     * The attributes that are mass assignable.
     *
     *@var array
     */
    protected $fillable = ['locale', 'post_id', 'title', 'content', 'description', 'keywords'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Sluggable attribute's name
     *
     * @var string
     */
    protected $sluggable = 'title';
}
