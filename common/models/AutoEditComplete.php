<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "auto_edit_complete".
 *
 * @property int $order_id
 */
class AutoEditComplete extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_edit_complete';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
        ];
    }
}
