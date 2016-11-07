<?php

class SimpleCastingTest extends TestCase
{
    protected $caster;

    public function setUp()
    {
        parent::setUp();

        $this->caster = new SimpleTestCaster();
    }

    /** @test */
    public function it_casts_a_column_with_a_simple_rename_cast()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('title', $result);
        $this->assertEquals($model->col1, $result['title']);
    }

    /** @test */
    public function it_casts_a_column_leaving_it_unchanged()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('col2', $result);
        $this->assertEquals($model->col2, $result['col2']);
    }

    /** @test */
    public function it_casts_a_collection_using_a_simple_cast_techniques()
    {
        $this->makeModels();

        $result = $this->caster->cast(TestModel::all());

        foreach ($result as $element) {
            $m = TestModel::find($element['id']);
            $this->assertArrayHasKey('title', $element);
            $this->assertEquals($m->col1, $element['title']);
            $this->assertEquals($m->col2, $element['col2']);
        }
    }
}

class SimpleTestCaster extends \Koch\Casters\Caster
{
    protected function castRules()
    {
        return [
            'id',
            'col1' => 'title',
            'col2'
        ];
    }
}

