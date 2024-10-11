<?php

namespace Pharaonic\Laravel\Translatable;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pharaonic\Laravel\Translatable\Traits\Actions;
use Pharaonic\Laravel\Translatable\Traits\Scopes;

trait Translatable
{
    use Scopes, Actions;

    /**
     * Current Locale
     *
     * @var string|null
     */
    protected $currentTranslatableLocale = null;

    /**
     * Catched translations list
     *
     * @var array|null
     */
    protected $catchedTranslations = null;


    /**
     * Init Translatable
     *
     * @return void
     */
    public function initializeTranslatable()
    {
        $this->currentTranslatableLocale = app()->getLocale();

        if (isset($this->translationsKey)) {
            if (in_array($this->translationsKey, ['translations', 'locales']))
                throw new \Exception('You cannot use `translations` or `locales` as translations key.');
            $this->fillable[] = $this->translationsKey;
        } else {
            $this->fillable[] = 'locale';
        }
    }

    /**
     * Boot Translatable (retrieved / saved / deleting)
     *
     * @return void
     */
    public static function bootTranslatable()
    {
        static::saving(function (Model $model) {
            // Getting Translations Key
            $key = $model->translationsKey ?? 'locale';

            // Remove Translatable Attributes
            foreach ($model->translatableAttributes as $attr)
                unset($model->{$attr});

            // Catch Translations Data
            $model->catchedTranslations = $model->{$key} ?? null;

            if (isset($model->{$key}))
                unset($model->{$key});

            if (!is_null($model->catchedTranslations) && !is_array($model->catchedTranslations))
                throw new Exception($model->$key . ' attribute should be array.');
        });

        static::saved(function (Model $model) {
            $model->createOrUpdateCatchedTranslations();
            $model->saveTranslations();
        });
    }

    /**
     * Create or Update Cated Translations on SAVED Event.
     *
     * @return void
     */
    protected function createOrUpdateCatchedTranslations()
    {
        if ($this->catchedTranslations && is_array($this->catchedTranslations)) {
            array_walk($this->catchedTranslations, function ($data, $locale) {
                foreach ($data as $key => $value) {
                    $this->translateOrNew($locale)->{$key} = $value;
                }
            });
        }
    }

    /**
     * Get Translatable table's name
     *
     * @return string
     */
    public function getTranslatableTable()
    {
        return Str::snake(Str::pluralStudly(class_basename($this) . 'Translation'));
    }

    /**
     * Get Translatable model's name
     *
     * @return string
     */
    public function getTranslatableModel()
    {
        return __CLASS__ . 'Translation';
    }

    /**
     * Get Translatable model's field
     *
     * @return string
     */
    public function getTranslatableField()
    {
        return Str::snake(Str::studly(class_basename($this))) . '_' . $this->getKeyName();
    }

    /**
     * Get Prepared Traslations
     *
     * @return Collection
     */
    protected function preparedTranslations()
    {
        if (isset($this->getRelation('translations')[0])) {
            $list = $this->getRelation('translations')->keyBy('locale');
            $this->setRelation('translations', $list);
            return $list;
        }

        return $this->getRelation('translations');
    }

    /**
     * Getting Translations
     *
     * @return Collection
     */
    public function getTranslationsAttribute()
    {
        if ($this->relationLoaded('translations'))
            return $this->preparedTranslations();

        $list = $this->translations()->get()->keyBy('locale');
        $this->setRelation('translations', $list);
        return $list;
    }

    /**
     * Save All Dirty Translations
     *
     * @return void
     */
    protected function saveTranslations()
    {
        if ($this->relationLoaded('translations'))
            foreach ($this->getRelation('translations') as $item) {
                if ($item->isDirty()) {
                    if (!$item->{$this->getTranslatableField()})
                        $item->{$this->getTranslatableField()} = $this->getKey();

                    $item->save();
                }
            }
    }

    /**
     * Delete All Translations
     *
     * @return void
     */
    protected function deleteTranslations()
    {
        $this->translations()->delete();
    }

    /**
     * Set the given relationship on the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        parent::setRelation($relation, $value);

        if (isset($this->translatableAttributes) && $this->relationLoaded('translations')) {
            foreach ($this->translatableAttributes as $attr) {
                $this->setAttribute($attr, $this->translateOrDefault()->{$attr} ?? null);
            }
        }

        return $this;
    }
}
