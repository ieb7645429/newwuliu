<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "small_print_order".
 *
 * @property int $id
 * @property string $print_content
 */
class SmallPrintOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'small_print_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id'], 'integer'],
            [['print_content'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'print_content' => 'Print Content',
        ];
    }
}
