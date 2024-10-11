<?php

namespace Pharaonic\Laravel\Translatable\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;

trait Scopes
{
    /**
     * Getting Translated Records
     *
     * @param Builder $query
     * @param string $locale
     * @return Builder
     */
    public function scopeTranslated(Builder $query, string $locale = null)
    {
        if (!$locale) return $query->with('translations');

        return $query->with('translations')->whereHas('translations', function (Builder $q) use ($locale) {
            $q->where('locale', '=', $locale);
        });
    }

    /**
     * Getting NOT Translated Records
     *
     * @param Builder $query
     * @param string $locale
     * @return Builder
     */
    public function scopeNotTranslated(Builder $query, string $locale = null)
    {
        $locale = $locale ?? $this->currentTranslatableLocale;

        return $query->with('translations')->whereDoesntHave('translations', function (Builder $q) use ($locale) {
            $q->where('locale', '=', $locale);
        });
    }

    /**
     * Getting Translated Records has been Sorted
     *
     * @param Builder $query
     * @param string $field
     * @param string $method
     * @return Builder
     */
    public function scopeTranslatedSorting(Builder $query, string $locale, string $field, string $method = 'asc')
    {
        $table = $this->getTable();
        $key = $this->getKeyName();
        $translatableTable = $this->getTranslatableTable();
        $translatableField = $this->getTranslatableField();

        return $query
            ->with('translations')
            ->select($table . '.*')
            ->join($translatableTable, function (JoinClause $join) use ($locale, $field, $table, $key, $translatableTable, $translatableField) {
                $join->on($translatableTable . '.' . $translatableField, '=', $table . '.' . $key)
                    ->where($translatableTable . '.locale', '=', $locale);
            })->orderBy($translatableTable . '.' . $field, $method)
            ->orderBy($table . '.' . $key, $method);
    }

    /**
     * Getting Translated Records (Where)
     *
     * @param Builder $query
     * @param string $locale
     * @return Builder
     */
    public function scopeWhereTranslation(Builder $query, string $field, $value, string $locale = null, string $action = 'whereHas', string $op = '=')
    {
        return $query->with('translations')->$action('translations', function (Builder $query) use ($field, $value, $locale, $op) {
            $query->where($this->getTranslatableTable() . '.' . $field, $op, $value);

            if ($locale)
                $query->where($this->getTranslatableTable() . '.locale', $op, $locale);
        });
    }

    /**
     * Getting Translated Records (or Where)
     *
     * @param Builder $query
     * @param string $locale
     * @return Builder
     */
    public function scopeOrWhereTranslation(Builder $query, string $field, $value, string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $field, $value, $locale, 'orWhereHas');
    }

    /**
     * Getting Translated Records (Like)
     *
     * @param Builder $query
     * @param string $locale
     * @return Builder
     */
    public function scopeWhereTranslationLike(Builder $query, string $field, $value, string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $field, $value, $locale, 'whereHas', 'LIKE');
    }

    /**
     * Getting Translated Records (or Like)
     *
     * @param Builder $query
     * @param string $locale
     * @return Builder
     */
    public function scopeOrWhereTranslationLike(Builder $query, string $field, $value, string $locale = null)
    {
        return $this->scopeWhereTranslation($query, $field, $value, $locale, 'orWhereHas', 'LIKE');
    }
}
