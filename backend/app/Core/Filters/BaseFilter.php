<?php

namespace App\Core\Filters;

use Illuminate\Support\Collection;
use App\Core\Contracts\FilterInterface;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class BaseFilter implements FilterInterface
{
    const DEFAULT_TIMEZONE = 'Asia/Ho_Chi_Minh';

    /**
     * @var \Illuminate\Support\Collection
     */
    protected static $grabInputs;

    /**
     * @var array
     */
    protected static $optionalInputs = [];

    /**
     * Grab other input for using in the filter
     *
     * @param \Illuminate\Support\Collection $data
     */
    public static function grabInputs(Collection $data)
    {
        self::$grabInputs = $data->only(self::$optionalInputs);
    }

    public static function escape(string $string)
    {
        $string = str_replace('\\', '\\\\', mb_strtolower($string));
        return addcslashes($string, '%_');
    }

    /**
     * Apply the filter
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Builder $model
     * @param mixed $input
     * @param mixed $repository
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public static function apply($model, $input, $repository = null)
    {
        return $model;
    }
}
