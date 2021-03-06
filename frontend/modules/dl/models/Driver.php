<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "driver".
 *
 * @property int $driver_id
 * @property int $logistics_car_id 物流车表
 * @property int $member_id 用户表id
 * @property string $add_time 生成时间
 */
class Driver extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'driver';
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
            [['logistics_car_id', 'member_id'], 'integer'],
            [['add_time'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'driver_id' => 'Driver ID',
            'logistics_car_id' => 'Logistics Car ID',
            'member_id' => 'Member ID',
            'add_time' => 'Add Time',
        ];
    }
    
    public function getCarTypeId($memberId) {
        $query = self::find();
        $result = $query -> select('logistics_car.car_type_id')
                         -> innerJoin('logistics_car', 'logistics_car.logistics_car_id = driver.logistics_car_id')
                         -> where('member_id = :member_id', ['member_id' => $memberId])
                         ->asArray()
                         -> one();
        return $result;
    } 
    
    public function getLogisticsCarInfo(){
        return $this->hasOne(LogisticsCar::className(), ['logistics_car_id'=>'logistics_car_id']);
    }
    //大司机专用
    public function getDriverDropList($same_city = null){
        if(empty($same_city)){
            $driver = $this::find()->all();
        }elseif($same_city==1){
            $driver = $this::find()->joinWith('logisticsCarInfo')->where(['car_type_id'=>1])->all();
        }elseif($same_city==2){
            $driver = $this::find()->joinWith('logisticsCarInfo')->where(['car_type_id'=>2])->all();
        }
        foreach($driver as $k=>$v){
            if(!empty(LogisticsRoute::findOne($v['logisticsCarInfo']['logistics_route_id'])->logistics_route_name)){
                $memberInfo = User::findOne($v['member_id']);
                $driverList[$v['member_id']]= LogisticsRoute::findOne($v['logisticsCarInfo']['logistics_route_id'])->logistics_route_name."-".$memberInfo->user_truename."-".$memberInfo->username;
            }
        }
        return $driverList;
    }
    public function getGoodsDriverDropList(){
        $order = new Goods();
        $res = $order::find()->select('driver_member_id')->groupBy('driver_member_id')->all();
        foreach($res as $k=>$v){
            if(!empty($v->driver_member_id)){
                $driverList[$v->driver_member_id]= User::findOne($v->driver_member_id)->user_truename;
            }
        }
        return $driverList;
    }
    
    public function getOrderDriverDropList(){
        $order = new LogisticsOrder();
        $res = $order::find()->select('driver_member_id')->groupBy('driver_member_id')->all();
        foreach($res as $k=>$v){
            if(!empty($v->driver_member_id)){
                $driverList[$v->driver_member_id]= User::findOne($v->driver_member_id)->user_truename;
            }
        }
        return $driverList;
    }
 public function getDriverRouteInfo($memberId) {
        $query = self::find();
        $result = $query -> select('logistics_route.logistics_route_name,logistics_route.same_city')
                         -> innerJoin('logistics_car', 'logistics_car.logistics_car_id = driver.logistics_car_id')
			             -> innerJoin('logistics_route','logistics_route.logistics_route_id = logistics_car.logistics_route_id')
                         -> where('member_id = :member_id', ['member_id' => $memberId])
                         ->asArray()
                         -> one();
        return $result;
    } 
}
