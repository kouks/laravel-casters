<?php

class MethodCastingTest extends TestCase
{
    protected $caster;

    public function setUp()
    {
        parent::setUp();

        $this->caster = new MethodTestCaster();
    }

    /** @test */
    public function it_casts_a_column_using_a_class_method_cast()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('title', $result);
        $this->assertEquals($model->col1.$model->col3, $result['title']);
    }

    /** @test */
    public function it_casts_a_collection_using_a_class_method_cast()
    {
        $this->makeModels();

        $result = $this->caster->cast(TestModel::all());

        foreach ($result as $element) {
            $m = TestModel::find($element['id']);
            $this->assertArrayHasKey('body', $element);
            $this->assertEquals($m->col2.$m->col4, $element['body']);
        }
    }
}

class MethodTestCaster extends \Koch\Casters\Caster
{
    protected function castRules()
    {
        return [
            'id',
            'title' => '@title',
            'body' => '@body',
        ];
    }

    public function title(TestModel $m)
    {
        return $m->col1 . $m->col3;
    }

    public function body(TestModel $m)
    {
        return $m->col2 . $m->col4;
    }
}

