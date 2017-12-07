<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "car_type".
 *
 * @property int $car_type_id
 * @property string $car_type 车类型(同城,干线，百度等)
 * @property string $add_time 添加时间
 */
class CarType extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'car_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_type'], 'string', 'max' => 255],
            [['add_time'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'car_type_id' => 'Car Type ID',
            'car_type' => 'Car Type',
            'add_time' => 'Add Time',
        ];
    }
}
