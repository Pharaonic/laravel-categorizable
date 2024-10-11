<?php

namespace Pharaonic\Laravel\Translatable\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Translatable\Translatable;

class Post extends Model
{
    use Translatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['published'];

    /**
     * Translatable attributes names.
     *
     * @var array
     */
    protected $translatableAttributes = [];

    /**
     * Casting attributes
     *
     * @var array
     */
    protected $casts = ['published' => 'boolean'];
}
