<?php

class ClosureCastingTest extends TestCase
{
    protected $caster;

    public function setUp()
    {
        parent::setUp();

        $this->caster = new ClosureTestCaster();
    }

    /** @test */
    public function it_casts_a_column_using_a_closre_cast()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('test1test2', $result['title']);
    }

    /** @test */
    public function it_casts_a_collection_using_a_closre_cast()
    {
        $this->makeModels();

        $result = $this->caster->cast(TestModel::all());

        foreach ($result as $element) {
            $this->assertArrayHasKey('body', $element);
            $this->assertEquals('test3test4', $element['body']);
        }
    }
}

class ClosureTestCaster extends \Koch\Casters\Caster
{
    protected function castRules()
    {
        return [
            'title' => function (TestModel $m) {
                return $m->col1 . $m->col2;
            },
            'body' => function (TestModel $m) {
                return $m->col3 . $m->col4;
            },
        ];
    }
}

