<?php

namespace Koch\Casters\Behavior;

use Koch\Casters\Contracts\Caster;

trait Castable
{
    /**
     * Casts either a collection or a single model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder
     * @param  \Koch\Casters\Contracts\Caster
     * @return array
     */
    public function scopeCast($query, Caster $caster)
    {
        if ($this->exists) {
            return $caster->cast($this);
        }

        return $caster->cast($query->get());
    }
}
