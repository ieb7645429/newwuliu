<?php

namespace common\models;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "parts".
 *
 * @property string $parts_id 索引ID
 * @property string $parts_name 配件名称
 * @property int $parts_sort 排序
 */
class Parts extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%parts}}';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parts_id' => '配件ID',
            'parts_name' => '配件名称',
            'parts_sort' => '排序',
        ];
    }


    /*
     * 获取配件列表
     * */
    public static function getParts()
    {
        return ArrayHelper::map(static::find()->asArray()->all(),'parts_id','parts_name');
    }
}
