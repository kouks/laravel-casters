<?php

namespace Koch\Casters\Behavior;

use Illuminate\Database\Eloquent\Model;

trait BuildsCastQueries
{
    /**
     * Builds up the cast query.
     *
     * @param  string  $old
     * @param  string  $query
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  &$transformed
     * @return array
     */
    protected function parse($old, $query, Model $model, &$transformed)
    {
        $key = $old;
        $value = $model->$old;

        // Many explodes here. We go through all of the queries separated
        // by '|', then parse its arguments after ':', which are separated
        // by ','. Then we call a corresponding method for each of
        // those queries, letting them adjust the key - value pair,
        // which is then returned.
        foreach (explode('|', $query) as $command) {
            list($function, $args) = explode(':', $command);

            $args = array_merge([&$key, &$value, $model], explode(',', $args));

            call_user_func_array([$this, $function], $args);
        }

        return $transformed[$key] = $value;
    }

    /**
     * Adjusts the name of given field.
     *
     * @param  string  &$key
     * @param  string  &$value
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $newName
     * @return void
     */
    protected function name(&$key, &$value, Model $model, $newName)
    {
        $key = $newName;
    }

    /**
     * Adjusts the data type of given field.
     *
     * @param  string  &$key
     * @param  string  &$value
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $type
     * @return void
     */
    protected function type(&$key, &$value, Model $model, $type)
    {
        settype($value, $type);
    }
}
