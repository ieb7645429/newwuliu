<?php

namespace common\yjmodels;

use Yii;

/**
 * This is the model class for table "qp_order".
 *
 * @property int $order_id 订单索引id
 * @property string $order_sn 订单编号
 * @property string $pay_sn 支付单号
 * @property string $store_id 卖家店铺id
 * @property string $store_name 卖家店铺名称
 * @property string $buyer_id 买家id
 * @property string $buyer_name 买家姓名
 * @property string $buyer_email 买家电子邮箱
 * @property string $add_time 订单生成时间
 * @property string $payment_code 支付方式名称代码
 * @property string $payment_time 支付(付款)时间
 * @property string $finnshed_time 订单完成时间
 * @property string $goods_amount 商品总价格
 * @property string $order_amount 订单总价格
 * @property string $rcb_amount 充值卡支付金额
 * @property string $pd_amount 预存款支付金额
 * @property int $shipping 运费：0包邮；1线下付
 * @property string $shipping_fee 运费
 * @property int $evaluation_state 评价状态 0未评价，1已评价，2已过期未评价
 * @property int $order_state 订单状态：0(已取消)10(默认):未付款;20:已付款;30:已发货;40:已收货;
 * @property int $refund_state 退款状态:0是无退款,1是部分退款,2是全部退款
 * @property int $lock_state 锁定状态:0是正常,大于0是锁定,默认是0
 * @property int $delete_state 删除状态0未删除1放入回收站2彻底删除
 * @property string $refund_amount 退款金额
 * @property string $delay_time 延迟时间,默认为0
 * @property int $order_from 1WEB2mobile
 * @property string $shipping_code 物流单号
 * @property int $is_procurement 是否为采购订单
 * @property string $procurement_id
 * @property int $touid 关联保险商ID
 * @property int $tolmuid 关联的区域合作商ID
 * @property int $islook 是否标记0否，1是
 * @property int $is_lot 退货时是否需要后台处理（0不需要,1需要）
 * @property int $is_recive 是否已接收：1接收; 2未接收
 * @property string $recive_time 接收时间
 * @property string $package_fee 包装费
 * @property int $shipping_state 拆单状态0为正常10位拆单
 * @property string $shipping_date 收货时间
 * @property int $is_balance 订单价钱是否存入个人余额(1未,2已存)
 * @property string $split_order_price 拆分订单前的价钱
 * @property string $back_order_price 退款前的订单价钱
 * @property int $back_state 是否退过订单(1没退过，2退过)
 * @property int $invoice 是否开发票 0:不开  1:开
 * @property int $order_type 订单类型(0求购 1反推,3卖家上传报价清单)
 * @property int $is_receive_goods 1为未收货2为已收货
 * @property int $is_receive_back_goods 1未收到退货,2收到退货
 * @property string $is_dispose 1已处理2未处理
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'qp_order';
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
            [['pay_sn', 'store_id', 'store_name', 'buyer_id', 'buyer_name'], 'required'],
            [['pay_sn', 'store_id', 'buyer_id', 'add_time', 'payment_time', 'finnshed_time', 'shipping', 'evaluation_state', 'order_state', 'refund_state', 'lock_state', 'delete_state', 'delay_time', 'order_from', 'is_procurement', 'procurement_id', 'touid', 'tolmuid', 'islook', 'is_lot', 'is_recive', 'recive_time', 'shipping_state', 'is_balance', 'back_state', 'invoice', 'order_type', 'is_receive_goods', 'is_receive_back_goods'], 'integer'],
            [['goods_amount', 'order_amount', 'rcb_amount', 'pd_amount', 'shipping_fee', 'refund_amount', 'package_fee', 'split_order_price', 'back_order_price'], 'number'],
            [['order_sn'], 'string', 'max' => 30],
            [['store_name', 'buyer_name', 'shipping_code'], 'string', 'max' => 50],
            [['buyer_email'], 'string', 'max' => 80],
            [['payment_code'], 'string', 'max' => 10],
            [['shipping_date'], 'string', 'max' => 255],
            [['is_dispose'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_sn' => 'Order Sn',
            'pay_sn' => 'Pay Sn',
            'store_id' => 'Store ID',
            'store_name' => 'Store Name',
            'buyer_id' => 'Buyer ID',
            'buyer_name' => 'Buyer Name',
            'buyer_email' => 'Buyer Email',
            'add_time' => 'Add Time',
            'payment_code' => 'Payment Code',
            'payment_time' => 'Payment Time',
            'finnshed_time' => 'Finnshed Time',
            'goods_amount' => 'Goods Amount',
            'order_amount' => 'Order Amount',
            'rcb_amount' => 'Rcb Amount',
            'pd_amount' => 'Pd Amount',
            'shipping' => 'Shipping',
            'shipping_fee' => 'Shipping Fee',
            'evaluation_state' => 'Evaluation State',
            'order_state' => 'Order State',
            'refund_state' => 'Refund State',
            'lock_state' => 'Lock State',
            'delete_state' => 'Delete State',
            'refund_amount' => 'Refund Amount',
            'delay_time' => 'Delay Time',
            'order_from' => 'Order From',
            'shipping_code' => 'Shipping Code',
            'is_procurement' => 'Is Procurement',
            'procurement_id' => 'Procurement ID',
            'touid' => 'Touid',
            'tolmuid' => 'Tolmuid',
            'islook' => 'Islook',
            'is_lot' => 'Is Lot',
            'is_recive' => 'Is Recive',
            'recive_time' => 'Recive Time',
            'package_fee' => 'Package Fee',
            'shipping_state' => 'Shipping State',
            'shipping_date' => 'Shipping Date',
            'is_balance' => 'Is Balance',
            'split_order_price' => 'Split Order Price',
            'back_order_price' => 'Back Order Price',
            'back_state' => 'Back State',
            'invoice' => 'Invoice',
            'order_type' => 'Order Type',
            'is_receive_goods' => 'Is Receive Goods',
            'is_receive_back_goods' => 'Is Receive Back Goods',
            'is_dispose' => 'Is Dispose',
        ];
    }
    
    /**
     * 查询友件订单是否还有未发货订单
     * @param unknown $memberName
     * @return boolean|unknown
     */
    public function getOrder($memberName = null)
    {
        if($memberName === null)
        {
            return false;
        }
        $member = Store::find()->where(['member_name'=>$memberName])->one();
        if(empty($member))
        {
            return false;
        }
        $condition['store_id'] = $member->store_id;
        $condition['order_state'] = 10;
        $datas = self::find()->select('order_sn')->where($condition)->asArray()->all();
        if($datas)
        {
            return $datas;
        }
        return false;
    }

}
