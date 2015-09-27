<?php

namespace yii2tech\tests\unit\ar\position;

use yii2tech\ar\position\PositionBehavior;
use yii2tech\tests\unit\ar\position\data\Item;

class PositionBehaviorTest extends TestCase
{
    /**
     * Asserts if records in the test table are in list order.
     */
    public function assertListCorrect()
    {
        $records = Item::find()
            ->orderBy(['position' => SORT_ASC])
            ->all();
        foreach ($records as $recordNumber => $record) {
            $this->assertEquals($record->position, $recordNumber + 1, 'List positions have been broken!');
        }
    }

    // Tests :

    public function testMovePrev() {

        /* @var $currentRecord Item|PositionBehavior */
        /* @var $previousRecord Item|PositionBehavior */
        /* @var $refreshedCurrentRecord Item|PositionBehavior */
        /* @var $refreshedPreviousRecord Item|PositionBehavior */

        $currentPosition = 2;
        $currentRecord = Item::findOne(['position' => $currentPosition]);
        $previousRecord = Item::findOne(['position' => $currentPosition - 1]);

        $this->assertTrue($currentRecord->movePrev(), 'Unable to move record to the prev!');

        $this->assertEquals($currentPosition - 1, $currentRecord->position, 'While moving record to the prev current object does not updated!');

        $refreshedCurrentRecord = Item::findOne($currentRecord->getPrimaryKey());

        $this->assertEquals($currentPosition - 1, $refreshedCurrentRecord->position, 'While moving record to the prev wrong position granted!');

        $refreshedPreviousRecord = Item::findOne($previousRecord->getPrimaryKey());
        $this->assertEquals($currentPosition, $refreshedPreviousRecord->position, 'While moving record to the prev wrong position granted to the previous record!');

        $this->assertListCorrect();
    }

    public function testMoveNext()
    {
        /* @var $currentRecord Item|PositionBehavior */
        /* @var $nextRecord Item|PositionBehavior */
        /* @var $refreshedCurrentRecord Item|PositionBehavior */
        /* @var $refreshedNextRecord Item|PositionBehavior */

        $currentPosition = 3;
        $currentRecord = Item::findOne(['position' => $currentPosition]);
        $nextRecord = Item::findOne(['position' => $currentPosition + 1]);

        $this->assertTrue($currentRecord->moveNext(), 'Unable to move record to the next!');

        $this->assertEquals($currentPosition + 1, $currentRecord->position, 'While moving record to the next current object does not updated!');

        $refreshedCurrentRecord = Item::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($currentPosition + 1, $refreshedCurrentRecord->position, 'While moving record to the next wrong position granted!');

        $refreshedNextRecord = Item::findOne($nextRecord->getPrimaryKey());
        $this->assertEquals($currentPosition, $refreshedNextRecord->position, 'While moving record to the next wrong position granted to the next record!');

        $this->assertListCorrect();
    }

    public function testMoveFirst()
    {
        /* @var $currentRecord Item|PositionBehavior */
        /* @var $refreshedCurrentRecord Item|PositionBehavior */

        $currentPosition = 3;
        $currentRecord = Item::findOne(['position' => $currentPosition]);

        $this->assertTrue($currentRecord->moveFirst(), 'Unable to move record to first!');

        $this->assertEquals(1, $currentRecord->position, 'While moving record first current object does not updated!');

        $refreshedCurrentRecord = Item::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals(1, $refreshedCurrentRecord->position, 'While moving record to first wrong position granted!');

        $this->assertListCorrect();
    }

    public function testMoveLast()
    {
        /* @var $currentRecord Item|PositionBehavior */
        /* @var $refreshedCurrentRecord Item|PositionBehavior */

        $recordsCount = Item::find()->count();

        $currentPosition = 2;
        $currentRecord = Item::findOne(['position' => $currentPosition]);

        $this->assertTrue($currentRecord->moveLast(), 'Unable to move record to last!');

        $this->assertEquals($recordsCount, $currentRecord->position, 'While moving record last current object does not updated!');

        $refreshedCurrentRecord = Item::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($recordsCount, $refreshedCurrentRecord->position, 'While moving record to last wrong position granted!');

        $this->assertListCorrect();
    }

    public function testMoveToPosition()
    {
        /* @var $currentRecord Item|PositionBehavior */
        /* @var $refreshedCurrentRecord Item|PositionBehavior */

        $currentPosition = 2;
        $currentRecord = Item::findOne(['position' => $currentPosition]);

        $positionToMove = 3;
        $this->assertTrue($currentRecord->moveToPosition($positionToMove), 'Unable to move record to the specific position down!');

        $this->assertEquals($positionToMove, $currentRecord->position, 'While moving record to the specific position down current object does not updated!');

        $refreshedCurrentRecord = Item::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($positionToMove, $refreshedCurrentRecord->position, 'Unable to move record to the specific position down correctly!');

        $currentPosition = 3;
        $currentRecord = Item::findOne(['position' => $currentPosition]);
        $positionToMove = 2;

        $this->assertTrue($currentRecord->moveToPosition($positionToMove), 'Unable to move record to the specific position up!');

        $this->assertEquals($positionToMove, $currentRecord->position, 'While moving record to the specific position up current object does not updated!');

        $refreshedCurrentRecord = Item::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($positionToMove, $refreshedCurrentRecord->position, 'Unable to move record to the specific position up correctly!');

        $this->assertListCorrect();
    }

    public function testInsert()
    {
        $newRecord = new Item();
        $newRecord->name = 'new record';
        $newRecord->save();
        $this->assertEquals($newRecord->position, Item::find()->count(), 'Wrong position for new record!');

        $newRecord = new Item();
        $newRecord->name = 'new record positioned';
        $newRecord->position = 3;
        $newRecord->save();
        $this->assertEquals($newRecord->position, 3, 'Unable to explicitly set position for new record!');

        $this->assertListCorrect();
    }

    /**
     * @depends testMoveToPosition
     */
    public function testUpdate()
    {
        /* @var $currentRecord Item|PositionBehavior */

        $currentPosition = 2;
        $currentRecord = Item::findOne(['position' => $currentPosition]);

        $newPosition = $currentPosition - 1;
        $currentRecord->position = $newPosition;

        $currentRecord->save();

        $this->assertEquals($currentRecord->position, $newPosition, 'While saving, position attribute value has been lost!');

        $this->assertListCorrect();
    }
}