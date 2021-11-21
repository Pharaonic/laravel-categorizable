<?php

namespace Pharaonic\Laravel\Categorizable\Traits;

use Illuminate\Database\Eloquent\Model;
use Pharaonic\Laravel\Categorizable\Models\Categorizable as CategorizableModel;
use Pharaonic\Laravel\Categorizable\Models\Category;

trait Categorizable
{
    /**
     * Boot the categorizable trait for the model.
     *
     * @return void
     */
    public static function bootCategorizable()
    {
        static::deleting(function (self $model) {
            $model->categories()->detach();
        });
    }

    /**
     * Prepare Categories IDs
     *
     * @param mixed ...$categories
     * @return array
     */
    private function prepareCategoriesIds(...$categories)
    {
        $categories = $categories[0];

        if ($categories[0] instanceof \Illuminate\Database\Eloquent\Collection) {
            return $categories[0]->modelKeys();
        } elseif (is_array($categories[0])) {
            $categories = $categories[0];
        }

        foreach ($categories as $k => &$category) {
            if (is_int($category)) {
                continue;
            } elseif ($category instanceof Model) {
                $category = $category->getKey();
            } else {
                throw new \Exception('You have to pass Keys or Models or Eloquent Collection');
            }
        }

        return $categories;
    }

    /**
     * Attach the model to Categories
     *
     * @param array|model|int ...$categories
     * @return void
     */
    public function categorize(...$categories)
    {
        $ids = $this->prepareCategoriesIds($categories);
        $this->categories()->sync($ids, false);

        return $this;
    }

    /**
     * Detach the model from Categories
     *
     * @param array|model|int ...$categories
     * @return void
     */
    public function decategorize(...$categories)
    {
        $ids = $this->prepareCategoriesIds($categories);
        $this->categories()->detach($ids);

        return $this;
    }

    /**
     * Sync the model to Categories
     *
     * @param array|model|int ...$categories
     * @return void
     */
    public function syncCategories(...$categories)
    {
        $ids = $this->prepareCategoriesIds($categories);
        $this->categories()->sync($ids);

        return $this;
    }

    /**
     * Get all attached categories to the model.
     *
     * @param string|array|null $type
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories($type = null)
    {
        $result = $this->morphToMany(Category::class, 'categorizable', 'categorizables', 'categorizable_id', 'category_id');

        if (is_array($type)) {
            $result = $result->whereIn('categories.type', $type);
        } elseif (!empty($type)) {
            $result = $result->where('categories.type', '=', $type);
        }

        return $result;
    }

    /**
     * Get attached category to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphToOne
     */
    public function category()
    {
        return $this->hasOneThrough(
            Category::class,
            CategorizableModel::class,
            'categorizable_id',
            'id',
            'id',
            'category_id'
        )->where('categorizables.categorizable_type', '=', static::class)->select('categories.*');
    }
}
