<?php

class BuildsCastQueriesTest extends TestCase
{
    protected $caster;

    public function setUp()
    {
        parent::setUp();

        $this->caster = new QueryTestCaster();
    }
    /** @test */
    public function it_casts_a_column_name_using_cast_query()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('title', $result);
        $this->assertEquals('test1', $result['title']);
    }

    /** @test */
    public function it_casts_a_column_type_using_cast_query()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('col2', $result);
        $this->assertInternalType('int', $result['col2']);
    }

    /** @test */
    public function it_combines_multiple_cast_queries()
    {
        $model = $this->makeModel();

        $result = $this->caster->cast($model);

        $this->assertArrayHasKey('body', $result);
        $this->assertInternalType('bool', $result['body']);
        $this->assertEquals(true, $result['body']);
    }

    /** @test */
    public function it_casts_a_collection()
    {
        $this->makeModels();

        $result = $this->caster->cast(TestModel::all());

        foreach ($result as $element) {
            $this->assertArrayHasKey('title', $element);
            $this->assertEquals('test1', $element['title']);
        }
    }
}

class QueryTestCaster extends \Koch\Casters\Caster
{
    protected function castRules()
    {
        return [
            'col1' => '!name:title',
            'col2' => '!type:int',
            'col3' => '!name:body|type:bool',
        ];
    }
}

