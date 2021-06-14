<?php

namespace Pharaonic\Laravel\Categorizable\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Sluggable\Sluggable;

/**
 * @property integer $id
 * @property integer $category_id
 * @property string $locale
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * 
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class CategoryTranslation extends Model
{
    use Sluggable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['locale', 'category_id', 'title', 'description'];

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
