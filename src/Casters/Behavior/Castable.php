<?php

namespace Koch\Casters\Behavior;

use Koch\Casters\Contracts\Caster;

trait Castable
{
    /**
     * Casts either a collection or a single model.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Koch\Casters\Contracts\Caster|null  $caster
     * @return array
     */
    public function scopeCast($query, Caster $caster = null)
    {
        $caster = $caster ?: $this->findCaster();

        if ($this->exists) {
            return $caster->cast($this);
        }

        return $caster->cast($query->get());
    }

    /**
     * Resolves the caster name and returns its instance.
     *
     * @return \Koch\Casters\Contracts\Caster
     */
    protected function findCaster()
    {
        if (property_exists($this, 'caster')) {
            return new $this->caster;
        }

        $name = 'App\\Casters\\' . class_basename($this) . 'Caster';

        return new $name;
    }
}
