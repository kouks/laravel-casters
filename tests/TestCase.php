<?php

use Illuminate\Database\Capsule\Manager as DB;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected $faker;

    public function setUp()
    {
        $this->setUpDatabase();
        $this->migrateTables();

        $this->faker = Faker\Factory::create();
    }

    protected function setUpDatabase()
    {
        $db = new DB;

        $db->addConnection(['driver' => 'sqlite', 'database' => ':memory:']);
        $db->bootEloquent();
        $db->setAsGlobal();
    }

    protected function migrateTables()
    {
        DB::schema()->create('test_models', function ($table) {
            $table->increments('id');
            $table->string('col1');
            $table->string('col2');
            $table->string('col3');
            $table->string('col4');
            $table->string('col5');
            $table->timestamps();
        });
    }

    protected function makeModel()
    {
        $m = new TestModel();

        $m->col1 = $this->faker->unique()->word;
        $m->col2 = $this->faker->unique()->word;
        $m->col3 = $this->faker->unique()->word;
        $m->col4 = $this->faker->unique()->word;
        $m->col5 = $this->faker->unique()->word;

        $m->save();

        return $m;
    }

    protected function makeModels($count = 5)
    {
        foreach (range(1, $count) as $i) {
            $this->makeModel();
        }
    }
}

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    use Koch\Casters\Behavior\Castable;
}
