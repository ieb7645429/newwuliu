<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "api_order".
 *
 * @property int $id
 * @property string $order_sn 友件订单编号
 * @property string $order_amount 订单价钱
 * @property string $freight 运费
 * @property int $goods_price_state 订单价钱(2未收,1已收)
 * @property int $freight_state 运费状态(2未收,1已收)
 * @property string $ip 访问ip
 * @property string $add_time 添加时间
 * @property int $code code码
 */
class ApiOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_amount', 'freight'], 'number'],
            [['goods_price_state', 'freight_state', 'add_time', 'code'], 'integer'],
            [['order_sn'], 'string', 'max' => 30],
            [['ip'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_sn' => 'Order Sn',
            'order_amount' => 'Order Amount',
            'freight' => 'Freight',
            'goods_price_state' => 'Goods Price State',
            'freight_state' => 'Freight State',
            'ip' => 'Ip',
            'add_time' => 'Add Time',
            'code' => 'Code',
        ];
    }
}
