<?php

namespace App\Casters;

class TestModelCaster extends \Koch\Casters\Caster
{
    protected function castRules()
    {
        return [
            'id',
            'col1' => 'body',
        ];
    }
}
