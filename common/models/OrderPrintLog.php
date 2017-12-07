<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "order_print_log".
 *
 * @property int $order_id
 * @property int $type 订单类型（1发货，2返货）
 * @property int $terminus 落地点待处理打印（1已打印，2未打印）
 */
class OrderPrintLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_print_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'terminus'], 'integer'],
            [['order_id'], 'unique', 'targetAttribute' => ['order_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'terminus' => 'Terminus',
        ];
    }
    
    /**
     * 更新打印log
     * @param unknown $orderIds
     */
    public function saveTerminusPrintLog($orderIds) {
        foreach ($orderIds as $orderId) {
            $printLog = self::findOne($orderId);
            if($printLog) {
                if ($printLog->terminus == 1) {
                    continue;
                } else {
                    $printLog->terminus = 1;
                    $printLog->save();
                }
            } else {
                $log = new self();
                $log->order_id = $orderId;
                $log->terminus = 1;
                $log->save();
            }
        }
    }
    
    /**
     * 小码单打印状态修改,并返回未打印订单
     */
    public function smallPrintEdit($data){
        $driverConfig = new DriverConfig();
        $smallPrint = $driverConfig::findOne(Yii::$app->user->id);
        $list = array();
        
        foreach($data as $key => $value){
        $printLog = self::findOne($value['order_id']);
            if($printLog) {
                if ($printLog->small_print != 1) {
                    $printLog->small_print = 1;
                    $printLog->save();
                    $list[] = $value;
                }
            } else {
                $log = new self();
                $log->order_id = $value['order_id'];
                $log->small_print = 1;
                $log->save();
                $list[] = $value;
            }
        }
        if(empty($smallPrint)||$smallPrint->small_print_status==0){
            return $list;
        }else{
            return $data;
        }
    }
    
}
