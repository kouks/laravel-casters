<?php

namespace Koch\Casters;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Koch\Casters\Behavior\BuildsCastQueries;
use Koch\Casters\Contracts\Caster as CasterContract;

abstract class Caster implements CasterContract
{
    use BuildsCastQueries;

    /**
     * Determines the function sign.
     *
     * @var string
     */
    private $functionSign = '@';

    /**
     * Determines the query sign.
     *
     * @var string
     */
    private $querySign = '!';

    /**
     * Casts collection fields.
     *
     * @param  mixed  $model
     * @return array
     */
    public function cast($model)
    {
        if ($model instanceof Collection) {
            return $model->map([$this, 'cast'])->toArray();
        }

        if (empty($model)) {
            return;
        }

        $transformed = [];

        foreach ($this->castRules() as $old => $desired) {
            $this->resolveCast($old, $desired, $model, $transformed);
        }

        return $transformed;
    }

    /**
     * Resolves casts based on supplied array of arguments.
     *
     * @param  string  $old
     * @param  string|Closure  $desired
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  array  &$transformed
     * @return array
     */
    private function resolveCast($old, $desired, Model $model, &$transformed)
    {
        if ($desired instanceof Closure) {
            return $transformed[$old] = call_user_func($desired, $model);
        }

        if (is_string($desired) && strpos($desired, $this->functionSign) !== false) {
            return $transformed[$old] = call_user_func([$this, substr($desired, 1)], $model);
        }

        if (is_string($desired) && strpos($desired, $this->querySign) !== false) {
            return $this->parse($old, substr($desired, 1), $model, $transformed);
        }

        if (is_string($old)) {
            return $transformed[$desired] = $model->$old;
        }

        return $transformed[$desired] = $model->$desired;
    }

    /**
     * Returns the cast rules.
     *
     * @return array
     */
    abstract protected function castRules();
}
