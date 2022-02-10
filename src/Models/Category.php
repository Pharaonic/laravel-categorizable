<?php

namespace Pharaonic\Laravel\Categorizable\Models;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Categorizable\Models\Categorizable as ModelsCategorizable;
use Pharaonic\Laravel\Categorizable\Traits\Categorizable;
use Pharaonic\Laravel\Translatable\Translatable;

/**
 * @property integer $id
 * @property string|null $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property CategoryTranslation $translations
 * 
 * @author Moamen Eltouny (Raggi) <raggi@raggitech.com>
 */
class Category extends Model
{
    use Translatable, Categorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type'];

    /**
     * Translatable attributes names.
     *
     * @var array
     */
    protected $translatableAttributes = ['title', 'description'];

    /**
     * Setting Relationships
     *
     * @return void
     */
    public static function booted()
    {
        foreach (config('Pharaonic.categorizable.children', []) as $name => $modelNamespace) {
            static::resolveRelationUsing($name, function ($model) use ($modelNamespace) {
                return $model->morphedByMany($modelNamespace, 'categorizable');
            });
        }
    }

    public function children()
    {
        return $this->hasManyThrough(
            Category::class,
            ModelsCategorizable::class,
            'category_id',
            'id',
            'id',
            'categorizable_id',
        )->where('categorizables.categorizable_type', '=', static::class)->select('categories.*');
    }
}
