<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "auto_payment".
 *
 * @property int $order_id
 * @property string $add_time 生成时间
 * @property string $goods_price 订单价钱
 * @property int $goods_price_state 原收款状态
 */
class AutoPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'auto_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id'], 'required'],
            [['order_id', 'add_time', 'goods_price_state'], 'integer'],
            [['goods_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'add_time' => 'Add Time',
            'goods_price' => 'Goods Price',
            'goods_price_state' => 'Goods Price State',
        ];
    }
}
