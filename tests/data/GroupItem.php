<?php

namespace yii2tech\tests\unit\ar\position\data;

use yii\db\ActiveRecord;
use yii2tech\ar\position\PositionBehavior;

/**
 * @property integer $id
 * @property string $name
 * @property string $groupId
 * @property boolean $position
 */
class GroupItem extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
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