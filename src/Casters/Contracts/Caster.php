<?php

namespace Koch\Casters\Contracts;

interface Caster
{
    /**
     * Casts collection fields.
     *
     * @param  mixed  $model
     * @return array
     */
    public function cast($model);
}
