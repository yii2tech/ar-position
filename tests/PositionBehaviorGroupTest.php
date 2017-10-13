<?php

namespace yii2tech\tests\unit\ar\position;

use yii2tech\ar\position\PositionBehavior;
use yii2tech\tests\unit\ar\position\data\GroupItem;

class PositionBehaviorGroupTest extends TestCase
{
    /**
     * Asserts if records in the test table are in list order.
     */
    public function assertListCorrect()
    {
        $records = GroupItem::find()
            ->andWhere(['groupId' => 1])
            ->orderBy(['position' => SORT_ASC])
            ->all();
        foreach ($records as $recordNumber => $record) {
            $this->assertEquals($record->position, $recordNumber + 1, 'List positions have been broken!');
        }

        $records = GroupItem::find()
            ->andWhere(['groupId' => 2])
            ->orderBy(['position' => SORT_ASC])
            ->all();
        foreach ($records as $recordNumber => $record) {
            $this->assertEquals($record->position, $recordNumber + 1, 'List positions have been broken!');
        }
    }

    // Tests:

    public function testMovePrev()
    {
        /* @var $currentRecord GroupItem|PositionBehavior */
        /* @var $previousRecord GroupItem|PositionBehavior */
        /* @var $refreshedCurrentRecord GroupItem|PositionBehavior */
        /* @var $refreshedPreviousRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 2;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);
        $previousRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition - 1]);

        $this->assertTrue($currentRecord->movePrev(), 'Unable to move record to the prev!');
        $this->assertEquals($currentPosition - 1, $currentRecord->position, 'While moving record to the prev current object does not updated!');

        $refreshedCurrentRecord = GroupItem::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($currentPosition - 1, $refreshedCurrentRecord->position, 'While moving record to the prev wrong position granted!');

        $refreshedPreviousRecord = GroupItem::findOne($previousRecord->getPrimaryKey());
        $this->assertEquals($currentPosition, $refreshedPreviousRecord->position, 'While moving record to the prev wrong position granted to the previous record!');

        $this->assertListCorrect();
    }

    public function testMoveNext()
    {
        /* @var $currentRecord GroupItem|PositionBehavior */
        /* @var $nextRecord GroupItem|PositionBehavior */
        /* @var $refreshedCurrentRecord GroupItem|PositionBehavior */
        /* @var $refreshedNextRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 3;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);
        $nextRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition + 1]);

        $this->assertTrue($currentRecord->moveNext(), 'Unable to move record to the next!');

        $this->assertEquals($currentPosition + 1, $currentRecord->position, 'While moving record to the next current object does not updated!');

        $refreshedCurrentRecord = GroupItem::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($currentPosition + 1, $refreshedCurrentRecord->position, 'While moving record to the next wrong position granted!');

        $refreshedNextRecord = GroupItem::findOne($nextRecord->getPrimaryKey());
        $this->assertEquals($currentPosition, $refreshedNextRecord->position, 'While moving record to the next wrong position granted to the next record!');

        $this->assertListCorrect();
    }

    public function testMoveFirst()
    {
        /* @var $currentRecord GroupItem|PositionBehavior */
        /* @var $refreshedCurrentRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 4;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);

        $this->assertTrue($currentRecord->moveFirst(), 'Unable to move record to first!');

        $this->assertEquals(1, $currentRecord->position, 'While moving record first current object does not updated!');

        $refreshedCurrentRecord = GroupItem::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals(1, $refreshedCurrentRecord->position, 'While moving record to first wrong position granted!');

        $this->assertListCorrect();
    }

    public function testMoveLast()
    {
        /* @var $currentRecord GroupItem|PositionBehavior */
        /* @var $refreshedCurrentRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 2;
        $recordsCount = GroupItem::find()->andWhere(['groupId' => $groupId])->count();
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);

        $this->assertTrue($currentRecord->moveLast(), 'Unable to move record to last!');

        $this->assertEquals($recordsCount, $currentRecord->position, 'While moving record last current object does not updated!');

        $refreshedCurrentRecord = GroupItem::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($recordsCount, $refreshedCurrentRecord->position, 'While moving record to last wrong position granted!');

        $this->assertListCorrect();
    }

    public function testMoveToPosition()
    {
        /* @var $currentRecord GroupItem|PositionBehavior */
        /* @var $refreshedCurrentRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 2;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);

        $positionToMove = 3;
        $this->assertTrue($currentRecord->moveToPosition($positionToMove), 'Unable to move record to the specific position down!');

        $this->assertEquals($positionToMove, $currentRecord->position, 'While moving record to the specific position down current object does not updated!');

        $refreshedCurrentRecord = GroupItem::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($positionToMove, $refreshedCurrentRecord->position, 'Unable to move record to the specific position down correctly!');

        $currentPosition = 3;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);
        $positionToMove = 2;

        $this->assertTrue($currentRecord->moveToPosition($positionToMove), 'Unable to move record to the specific position up!');

        $this->assertEquals($positionToMove, $currentRecord->position, 'While moving record to the specific position up current object does not updated!');

        $refreshedCurrentRecord = GroupItem::findOne($currentRecord->getPrimaryKey());
        $this->assertEquals($positionToMove, $refreshedCurrentRecord->position, 'Unable to move record to the specific position up correctly!');

        $this->assertListCorrect();
    }

    public function testInsert()
    {
        $groupId = 2;

        $newRecord = new GroupItem();
        $newRecord->name = 'new record';
        $newRecord->groupId = $groupId;
        $newRecord->save();
        $this->assertEquals($newRecord->position, GroupItem::find()->andWhere(['groupId' => $groupId])->count(), 'Wrong position for new record!');

        $newRecord = new GroupItem();
        $newRecord->name = 'new record positioned';
        $newRecord->groupId = $groupId;
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
        /* @var $currentRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 2;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);

        $newPosition = $currentPosition-1;
        $currentRecord->position = $newPosition;

        $currentRecord->save();

        $this->assertEquals($currentRecord->position, $newPosition, 'While saving, position attribute value has been lost!' );

        $this->assertListCorrect();
    }

    /**
     * @depends testUpdate
     */
    public function testMoveBetweenGroups()
    {
        /* @var $currentRecord GroupItem|PositionBehavior */

        $groupId = 2;
        $currentPosition = 2;
        $currentRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => $currentPosition]);

        $newGroupId = 1;
        $currentRecord->groupId = $newGroupId;
        $currentRecord->save();

        $this->assertListCorrect();
    }

    /**
     * @depends testMoveNext
     */
    public function testGetIsFirst()
    {
        /* @var $firstRecord GroupItem|PositionBehavior */
        /* @var $refreshedRecord GroupItem|PositionBehavior */

        $groupId = 2;

        $firstRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => 1]);
        $this->assertTrue($firstRecord->getIsFirst());

        $firstRecord->moveNext();
        $this->assertFalse($firstRecord->getIsFirst());

        $refreshedRecord = GroupItem::findOne($firstRecord->id);
        $this->assertFalse($refreshedRecord->getIsFirst());
    }

    /**
     * @depends testMovePrev
     */
    public function testGetIsLast()
    {
        /* @var $lastRecord GroupItem|PositionBehavior */
        /* @var $refreshedRecord GroupItem|PositionBehavior */

        $groupId = 2;

        $lastRecord = GroupItem::find()
            ->andWhere(['groupId' => $groupId])
            ->orderBy(['position' => SORT_DESC])
            ->limit(1)
            ->one();
        $this->assertTrue($lastRecord->getIsLast());

        $lastRecord->movePrev();
        $this->assertFalse($lastRecord->getIsLast());

        $refreshedRecord = GroupItem::findOne($lastRecord->id);
        $this->assertFalse($refreshedRecord->getIsLast());
    }

    public function testFindNext()
    {
        /* @var $firstRecord GroupItem|PositionBehavior */
        /* @var $secondRecord GroupItem|PositionBehavior */
        /* @var $lastRecord GroupItem|PositionBehavior */

        $groupId = 2;

        $firstRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => 1]);

        $secondRecord = $firstRecord->findNext();
        $this->assertEquals(2, $secondRecord->position);

        $lastRecord = GroupItem::find()
            ->andWhere(['groupId' => $groupId])
            ->orderBy(['position' => SORT_DESC])
            ->limit(1)
            ->one();

        $this->assertNull($lastRecord->findNext());
    }

    public function testFindPrev()
    {
        /* @var $firstRecord GroupItem|PositionBehavior */
        /* @var $preLastRecord GroupItem|PositionBehavior */
        /* @var $lastRecord GroupItem|PositionBehavior */

        $groupId = 2;

        $lastRecord = GroupItem::find()
            ->andWhere(['groupId' => $groupId])
            ->orderBy(['position' => SORT_DESC])
            ->limit(1)
            ->one();

        $preLastRecord = $lastRecord->findPrev();
        $this->assertEquals($lastRecord->position - 1, $preLastRecord->position);

        $firstRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => 1]);
        $this->assertNull($firstRecord->findPrev());
    }

    public function testFindFirst()
    {
        /* @var $firstRecord GroupItem|PositionBehavior */
        /* @var $secondRecord GroupItem|PositionBehavior */

        $groupId = 2;

        $firstRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => 1]);
        $this->assertSame($firstRecord, $firstRecord->findFirst());

        $secondRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => 2]);
        $this->assertEquals($firstRecord->id, $secondRecord->findFirst()->id);
    }

    public function testFindLast()
    {
        /* @var $lastRecord GroupItem|PositionBehavior */
        /* @var $secondRecord GroupItem|PositionBehavior */

        $groupId = 2;

        $lastRecord = GroupItem::find()
            ->andWhere(['groupId' => $groupId])
            ->orderBy(['position' => SORT_DESC])
            ->limit(1)
            ->one();
        $this->assertSame($lastRecord->id, $lastRecord->findLast()->id);

        $secondRecord = GroupItem::findOne(['groupId' => $groupId, 'position' => 2]);
        $this->assertEquals($lastRecord->id, $secondRecord->findLast()->id);
    }
}