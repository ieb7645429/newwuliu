<?php

namespace frontend\modules\dl\models;

use Yii;

/**
 * This is the model class for table "return_order_time".
 *
 * @property int $order_id
 * @property string $price_time 物流价钱生成时间
 * @property string $sorter_time 分捡时间
 * @property string $ruck_time 装车时间
 * @property string $unload_time 卸货时间
 * @property string $signed_for_time 签收时间
 */
class ReturnOrderTime extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'return_order_time';
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
            [['price_time', 'sorter_time', 'ruck_time', 'unload_time', 'signed_for_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'price_time' => 'Price Time',
            'sorter_time' => 'Sorter Time',
            'ruck_time' => 'Ruck Time',
            'unload_time' => 'Unload Time',
            'signed_for_time' => 'Signed For Time',
        ];
    }
    
    /**
     * 更新物流时间表
     * @param unknown $upda
     * @param array $condition
     */
    public function upReturnOrderTime($upda = array(), $condition = array()){
        return $this::updateAll($upda, $condition);
    }
    
    
    /**
     * 判断更新时间
     * @param unknown $state
     * @param unknown $orderId
     */
    public function orderTimeswitch($state, $data)
    {
        $timeArray = array('50'=>'ruck_time', '10'=>'price_time', '70'=>'unload_time', '80'=>'signed_for_time', 'collection'=>'collection_time' ,'freight'=>'income_freight_time','price'=>'income_price_time' , 'pay_freight_time'=>'pay_freight_time', 'pay_price_time'=>'pay_price_time');
        return $this->upReturnOrderTime(array($timeArray[$state]=>time()), array('order_id'=>$data->order_id));
    }
}
