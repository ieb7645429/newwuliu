<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_order_common".
 *
 * @property int $order_id 订单索引id
 * @property string $store_id 店铺ID
 * @property string $shipping_time 配送时间
 * @property int $shipping_express_id 配送公司ID
 * @property string $evaluation_time 评价时间
 * @property string $evalseller_state 卖家是否已评价买家
 * @property string $evalseller_time 卖家评价买家的时间
 * @property string $order_message 订单留言
 * @property int $order_pointscount 订单赠送积分
 * @property int $voucher_price 代金券面额
 * @property string $voucher_code 代金券编码
 * @property string $deliver_explain 发货备注
 * @property int $daddress_id 发货地址ID
 * @property string $reciver_name 收货人姓名
 * @property string $reciver_info 收货人其它信息
 * @property string $reciver_province_id 收货人省级ID
 * @property string $reciver_city_id 收货人市级ID
 * @property string $invoice_info 发票信息
 * @property string $promotion_info 促销信息备注
 * @property string $dlyo_pickup_code 提货码
 * @property int $deliver_goods_city 	id
 * @property int $address_id 地址表的ID
 * @property int $logistics_company_id 物流公司id
 */
class OrderCommon extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_order_common';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['store_id', 'evalseller_time', 'reciver_name', 'reciver_info', 'address_id'], 'required'],
            [['store_id', 'shipping_time', 'shipping_express_id', 'evaluation_time', 'evalseller_time', 'order_pointscount', 'voucher_price', 'daddress_id', 'reciver_province_id', 'reciver_city_id', 'deliver_goods_city', 'address_id', 'logistics_company_id'], 'integer'],
            [['evalseller_state', 'deliver_explain'], 'string'],
            [['order_message'], 'string', 'max' => 300],
            [['voucher_code'], 'string', 'max' => 32],
            [['reciver_name'], 'string', 'max' => 50],
            [['reciver_info', 'invoice_info', 'promotion_info'], 'string', 'max' => 500],
            [['dlyo_pickup_code'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'store_id' => 'Store ID',
            'shipping_time' => 'Shipping Time',
            'shipping_express_id' => 'Shipping Express ID',
            'evaluation_time' => 'Evaluation Time',
            'evalseller_state' => 'Evalseller State',
            'evalseller_time' => 'Evalseller Time',
            'order_message' => 'Order Message',
            'order_pointscount' => 'Order Pointscount',
            'voucher_price' => 'Voucher Price',
            'voucher_code' => 'Voucher Code',
            'deliver_explain' => 'Deliver Explain',
            'daddress_id' => 'Daddress ID',
            'reciver_name' => 'Reciver Name',
            'reciver_info' => 'Reciver Info',
            'reciver_province_id' => 'Reciver Province ID',
            'reciver_city_id' => 'Reciver City ID',
            'invoice_info' => 'Invoice Info',
            'promotion_info' => 'Promotion Info',
            'dlyo_pickup_code' => 'Dlyo Pickup Code',
            'deliver_goods_city' => 'Deliver Goods City',
            'address_id' => 'Address ID',
            'logistics_company_id' => 'Logistics Company ID',
        ];
    }
}
