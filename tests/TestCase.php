<?php

namespace yii2tech\tests\unit\ar\position;

use yii\helpers\ArrayHelper;
use Yii;

/**
 * Base class for the test cases.
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();

        $this->setupTestDbData();
    }

    protected function tearDown()
    {
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($config = [], $appClass = '\yii\console\Application')
    {
        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => __DIR__,
            'vendorPath' => $this->getVendorPath(),
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
            ],
        ], $config));
    }

    /**
     * @return string vendor path
     */
    protected function getVendorPath()
    {
        return dirname(__DIR__) . '/vendor';
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
    }

    /**
     * Setup tables for test ActiveRecord
     */
    protected function setupTestDbData()
    {
        $db = Yii::$app->getDb();

        // Structure :

        $table = 'Item';
        $columns = [
            'id' => 'pk',
            'name' => 'string',
            'position' => 'integer',
        ];
        $db->createCommand()->createTable($table, $columns)->execute();

        $table = 'GroupItem';
        $columns = [
            'id' => 'pk',
            'name' => 'string',
            'groupId' => 'integer',
            'position' => 'integer',
        ];
        $db->createCommand()->createTable($table, $columns)->execute();

        // Data :

        $db->createCommand()->batchInsert('Item', ['name', 'position'], [
            ['item1', 1],
            ['item2', 2],
            ['item2', 3],
            ['item2', 4],
            ['item2', 5],
        ])->execute();

        $db->createCommand()->batchInsert('GroupItem', ['name', 'groupId', 'position'], [
            ['item1', 1, 1],
            ['item1', 2, 1],
            ['item2', 1, 2],
            ['item2', 2, 2],
            ['item2', 1, 3],
            ['item2', 2, 3],
            ['item2', 1, 4],
            ['item2', 2, 4],
            ['item2', 1, 5],
            ['item2', 2, 5],
        ])->execute();
    }
}
