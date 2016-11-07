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
    /** @test */
    public function it_implicitly_find_related_caster()
    {
        $model = $this->makeModel();

        $result = $model->cast();

        $this->assertArrayHasKey('body', $result);
        $this->assertEquals($model->col1, $result['body']);
    }

    /** @test */
    public function it_implicitly_find_related_caster_for_a_collection()
    {
        $this->makeModels();

        $result = TestModel::cast();

        foreach ($result as $element) {
            $m = TestModel::find($element['id']);
            $this->assertArrayHasKey('body', $element);
            $this->assertEquals($m->col1, $element['body']);
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

