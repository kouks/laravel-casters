<?php

class CastableTest extends TestCase
{
    protected $caster;

    public function setUp()
    {
        parent::setUp();

        $this->caster = new CastableTestCaster;
    }

    /** @test */
    public function it_allows_a_model_to_cast_itself()
    {
        $model = $this->makeModel();

        $result = $model->cast($this->caster);

        $this->assertArrayHasKey('title', $result);
        $this->assertEquals($model->col1, $result['title']);
    }

    /** @test */
    public function it_allows_using_a_cast_query_scope()
    {
        $this->makeModels();

        $result = TestModel::cast($this->caster);

        foreach ($result as $element) {
            $m = TestModel::find($element['id']);
            $this->assertArrayHasKey('title', $element);
            $this->assertEquals($m->col1, $element['title']);
        }
    }
}

class CastableTestCaster extends \Koch\Casters\Caster
{
    protected function castRules()
    {
        return [
            'id',
            'col1' => 'title',
        ];
    }
}

