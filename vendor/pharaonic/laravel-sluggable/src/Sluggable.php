<?php

namespace Pharaonic\Laravel\Sluggable;

use Illuminate\Database\Eloquent\Builder;

trait Sluggable
{
    /**
     * Sluggable Config List
     *
     * @var array
     */
    public static $sluggableConfig;

    /**
     * Init Sluggable Package
     * Add `slug` field to fillable list.
     *
     * @return void
     */
    public function initializeSluggable()
    {
        if (!$this->sluggable)
            throw new \Exception('You have to defined the public property `sluggable`.');

        $this->fillable[] = 'slug';
    }

    /**
     * Boot Sluggable Package
     * Create Eloquent Events (Creating, Updating)
     *
     * @return void
     */
    protected static function bootSluggable()
    {
        self::$sluggableConfig = config('Pharaonic.sluggable', include 'config.php');

        // Creating && Updating
        self::saving(function ($model) {
            if ((self::$sluggableConfig['onCreate'] && !$model->getKey()) || self::$sluggableConfig['onUpdate'] && $model->getKey())
                if ($model->isDirty($model->sluggable) && !$model->isDirty('slug'))
                    $model->slug = $model->generateSlug($model->{$model->sluggable});
        });
    }

    /**
     * Getting slug with the key.
     *
     * @return string
     */
    public function getSlugWithKeyAttribute()
    {
        $isTranslatable = substr(__CLASS__, -11) == 'Translation';
        $class = substr(__CLASS__, 0, -11);

        if ($isTranslatable && class_exists($class)) {
            $relation = strtolower(class_basename($class));

            if (method_exists($this, $relation))
                return implode('-', [$this->{$relation . '_id'}, $this->slug]);
        }

        return implode('-', [$this->getKey(), $this->slug]);
    }

    /**
     * Generate slug for specific record.
     *
     * @param string $string
     * @return string|null
     */
    private function generateSlug(string $string)
    {
        $string = slug($string);
        if (empty($string)) return null;

        if ($this->slug != $string)
            if (self::$sluggableConfig['unique']) {
                $count = self::where('slug', 'like', $string . '%')->count();
                $string = $count > 0 ? $string . '-' . $count : $string;
            }

        return $string;
    }

    /**
     * Conditional Where for Slug
     *
     * @param Builder $scope
     * @param string $slug
     * @return Builder
     */
    public function scopeWhereSlug(Builder $scope, string $slug)
    {
        return $scope->where('slug', $slug);
    }

    /**
     * Find a model by its slug.
     *
     * @param string $slug
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|null
     */
    public static function findBySlug(string $slug, array $columns = ['*'])
    {
        return static::whereSlug($slug)->first($columns);
    }

    /**
     * Find a model by its slug or throw an exception.
     *
     * @param string $slug
     * @param array $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function findBySlugOrFail(string $slug, array $columns = ['*'])
    {
        return static::whereSlug($slug)->firstOrFail($columns);
    }
}
