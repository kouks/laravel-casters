<?php

use Illuminate\Database\Capsule\Manager as DB;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->setUpDatabase();
        $this->migrateTables();
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

    protected function makeModel($str = 'test')
    {
        $m = new TestModel();

        $m->col1 = $str . '1';
        $m->col2 = $str . '2';
        $m->col3 = $str . '3';
        $m->col4 = $str . '4';
        $m->col5 = $str . '5';

        $m->save();

        return $m;
    }

    protected function makeModels($count = 5, $str = 'test')
    {
        foreach (range(1, $count) as $i) {
            $this->makeModel($str);
        }
    }
}

class TestModel extends \Illuminate\Database\Eloquent\Model
{
    //
}
