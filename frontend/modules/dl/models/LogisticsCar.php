<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "logistics_car".
 *
 * @property int $logistics_car_id
 * @property int $logistics_route_id 物流线路表id
 * @property int $car_type_id 车类型表id
 */
class LogisticsCar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_car';
    }
    
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_dl');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['logistics_route_id', 'car_type_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'logistics_car_id' => 'Logistics Car ID',
            'logistics_route_id' => 'Logistics Route ID',
            'car_type_id' => 'Car Type ID',
        ];
    }
    public function getDriverInfo(){
        return $this->hasOne(Driver::className(), ['logistics_car_id'=>'logistics_car_id']);
    }
    public function getCarType(){
        return $this->hasOne(CarType::className(), ['car_type_id'=>'car_type_id']);
    }
    /**
     * 获取车辆信息
     * @param driver_id
     * 靳健
     */
    public function getCarInfo($driver_id){
        $result = $this::find()->joinWith('driverInfo')->joinWith('carType')->where(['driver.member_id'=>$driver_id])->asArray()->one();
        return $result;
    }
}
