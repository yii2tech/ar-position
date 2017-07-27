<?php

namespace yii2tech\tests\unit\ar\position\data;

use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * @property int $id
 * @property string $name
 * @property string $groupId
 * @property int $position
 */
class GroupItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'positionBehavior' => [
                'class' => PositionBehavior::className(),
                'groupAttributes' => [
                    'groupId'
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'GroupItem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            ['groupId', 'required'],
        ];
    }
}