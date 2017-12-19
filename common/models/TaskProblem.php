<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "task_problem".
 *
 * @property int $id
 * @property string $table_name 表名
 * @property int $table_id 主键id
 * @property string $add_time
 */
class TaskProblem extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task_problem';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['table_id'], 'required'],
            [['table_id'], 'integer'],
            [['add_time'], 'safe'],
            [['table_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'table_name' => 'Table Name',
            'table_id' => 'Table ID',
            'add_time' => 'Add Time',
        ];
    }
}
