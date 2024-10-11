<?php

namespace Pharaonic\Laravel\Translatable\Traits;

use Illuminate\Database\Eloquent\Model;

trait Actions
{
    /**
     * Translations Relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translations()
    {
        return $this->hasMany($this->getTranslatableModel(), $this->getTranslatableField());
    }

    /**
     * Set Translations
     *
     * @param string $locale
     * @param Model $model
     * @return void
     */
    protected function addTranslatableWithLocale(string $locale, Model $model)
    {
        $list = $this->relationLoaded('translations') ? $this->getRelation('translations') : [];
        $list[$locale] = $model;
        $this->setRelation('translations', $list);
    }

    /**
     * Getting Translation
     *
     * @param string|null $locale
     * @return Model
     */
    protected function getTranslation(?string $locale = null): ?Model
    {
        $locale = $locale ?? $this->currentTranslatableLocale;

        $this->hasTranslation($locale);

        if (isset($this->translations[$locale])) {
            if (is_array($this->translations[$locale])) {
                $item = $this->getTranslatableModel();
                $item = new $item();
                $item->locale = $locale;
                $item->{$this->getTranslatableField()} = $this->getKey();
                $item->fill($this->translations[$locale]);
                $this->addTranslatableWithLocale($locale, $item);
            }

            return $this->translations[$locale];
        }

        return null;
    }

    /**
     * Getting Translation
     *
     * @param string $locale
     * @return Model|null
     */
    public function translate(string $locale = null): ?Model
    {
        return $this->getTranslation($locale);
    }

    /**
     * Getting Translation or Abort
     *
     * @param string $locale
     * @return Model
     */
    public function translateOrFail(string $locale): Model
    {
        return $this->getTranslation($locale) ?? abort(404);
    }

    /**
     * Getting Translation or Creating a new one
     *
     * @param string $locale
     * @return Model
     */
    public function translateOrNew(string $locale): Model
    {
        $item = $this->translate($locale) ?? null;

        if (!$item) {
            $item = $this->getTranslatableModel();
            $item = new $item();
            $item->locale = $locale;
            $item->{$this->getTranslatableField()} = $this->getKey();
            $this->addTranslatableWithLocale($locale, $item);
        }

        return $item;
    }

    /**
     * Getting Translation with current Locale or Default Locale
     *
     * @param string $locale
     * @return Model|null
     */
    public function translateOrDefault(string $locale = null)
    {
        return $this->getTranslation($locale) ?? $this->getTranslation(config('Pharaonic.translatable.default', 'en')) ?? null;
    }

    /**
     * Check has been translated
     *
     * @param string $locale
     * @return boolean
     */
    public function hasTranslation(string $locale = null)
    {
        if (!$this->relationLoaded('translations'))
            $this->translations;

        return $locale ? isset($this->getRelation('translations')[$locale]) : $this->getRelation('translations')->isNotEmpty();
    }

    /**
     * Getting Locales List
     *
     * @return array
     */
    public function getLocalesAttribute()
    {
        if (!$this->hasTranslation()) return [];
        return array_keys($this->translations->toArray());
    }
}
