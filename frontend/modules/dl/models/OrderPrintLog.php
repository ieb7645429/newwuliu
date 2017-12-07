<?php

namespace frontend\modules\dl\models;

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
}
