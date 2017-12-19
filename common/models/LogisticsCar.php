<?php

namespace common\models;

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

    //created by fenghuan
    const SCENARIO_CAR = 'car_number';
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CAR] = [];
        return $scenarios;
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_car';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_number'], 'required'],
            [['logistics_car_id', 'logistics_route_id', 'car_type_id'], 'integer'],
            ['car_number', 'string'],
        ];
    }

//    public function attributes()
//    {
//        return ['logistics_route_id', 'car_number', 'car_type_id'];
//    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'logistics_car_id' => 'Logistics Car ID',
            'logistics_route_id' => 'Logistics Route ID',
            'car_type_id' => 'Car Type ID',
            'car_number' => '车牌号',
        ];
    }

    /**
     * 通过线路id获取所有司机
     * @Author:Fenghuan
     * @param $condition
     * @param $fields
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getUsersByCondition($condition, $fields)
    {
        return self::find()->select($fields)->leftJoin('driver', 'driver.logistics_car_id = logistics_car.logistics_car_id')->where($condition)->asArray()->all();
    }

    /**
     * 根据线路获取所有司机
     * @Author:Fenghuan
     * @param $condition
     * @param $fields
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getDriversByRoute($condition, $fields)
    {
        return self::find()
            ->leftJoin('driver', 'logistics_car.logistics_car_id = driver.logistics_car_id')
            ->leftJoin('user', 'driver.member_id = user.id')
            ->select($fields)->where($condition)->asArray()->all();
    }

    /**
     * car -> driver
     * @Author:Fenghuan
     * @return \yii\db\ActiveQuery
     */
    public function getCarOfDriver()
    {
        return $this->hasOne(Driver::className(), ['logistics_car_id' => 'logistics_car_id'])->joinWith('driverJoinUser');
    }

    /**
     * 更新
     * @Author:Fenghuan
     * @param $data
     * @param $condition
     * @return int
     */
    public function updateCar($data, $condition)
    {
        return self::updateAll($data, $condition);
    }

    /**
     * 一个线路多个车
     * @Author:Fenghuan
     * @param $condition
     * @return array|\yii\db\ActiveRecord[]
     */
    public function selectCars($condition)
    {
        $data = self::find()->select(['car_number', 'logistics_car_id'])->where($condition)
            ->orderBy('logistics_car_id')
            ->indexBy('logistics_car_id')
            ->column();

        return $data;
    }

    /**
     * @Author:Fenghuan
     * @param $id
     * @return static
     */
    public static function findModel($id)
    {
        return self::findOne($id);
    }

    /**
     * @Author:Fenghuan
     * @param $condition
     * @return array|null|\yii\db\ActiveRecord
     */
    public function findOneModel($condition)
    {
        return self::find()->where($condition)->one();
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
