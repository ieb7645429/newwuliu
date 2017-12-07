<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistics_lines".
 *
 * @property string $order_id
 * @property int $driver_member_id 司机id
 * @property int $logistics_route_id 线路id
 * @property int $terminus_id 落地点id
 * @property int $same_city 是否同城(1是,2不是)
 * @property string $add_time 开始时间
 * @property string $end_time 结束时间
 * @property int $state 是否完成(1完成,2未完成)
 */
class LogisticsLines extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_lines';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['driver_member_id', 'logistics_route_id', 'terminus_id', 'same_city', 'add_time', 'end_time', 'state'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order Id',
            'driver_member_id' => 'Driver Member ID',
            'logistics_route_id' => 'Logistics Route ID',
            'terminus_id' => 'Terminus ID',
            'same_city' => 'Same City',
            'add_time' => 'Add Time',
            'end_time' => 'End Time',
            'state' => 'State',
        ];
    }
    
    /**
     * 添加Lines
     * @param unknown $arr
     */
    public function addLines($arr){
        $order = new LogisticsOrder();
        $goods = new Goods();
        $role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
        foreach($arr as $key => $value){
            $lines = new LogisticsLines();
            if(empty($lines::findOne($value))){
                $model = $order::findOne($value);
                //大司机封车获取司机id
                if($role==Yii::$app->params['roleDriverManagerCityWide']||$role==Yii::$app->params['roleDriverManager']){
                    $driver_member_id = $goods::find()->where(['and','order_id ='.$value,['<>','driver_member_id',0]])->asArray()->all()[0]['driver_member_id'];
                }else{
                    $driver_member_id = Yii::$app->user->id;
                }
                $lines->order_id = $model->order_id;
                $lines->driver_member_id = $driver_member_id;
                $lines->logistics_route_id = $model->logistics_route_id;
                $lines->terminus_id = $model->terminus_id;
                $lines->same_city = $model->same_city;
                if(!$lines->save()){
                    return false;
                }
            }
        }
        return true;
    }
    
    /**
     * 删除lines
     * @param unknown $order_id
     */
    public function delLines($order_id){
        $model = $this::findOne($order_id);
        if(!empty($model)){
            $model->delete();
        }
    }
	/**
	* 修改state状态
	* 入参 $where,$value
	**/
    public function ChangeState($where,$value){
			return $this::updateAll($value,$where);
	}

}
