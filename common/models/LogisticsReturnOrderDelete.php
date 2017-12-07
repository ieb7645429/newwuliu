<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "logistics_return_order_delete".
 *
 * @property int $order_id
 * @property string $logistics_sn 票号
 * @property string $ship_logistics_sn 发货时票号
 * @property string $goods_sn 物流单号
 * @property string $order_sn 订单编号
 * @property string $freight 运费
 * @property string $goods_price 商品价钱
 * @property string $make_from_price 制单费
 * @property int $goods_num 商品数量
 * @property int $order_state 订单一级状态(0取消，5用户开单，10开单，20分拣，30摆渡车，40货站分拣，50封车，60落地，70送货，80完成)
 * @property int $state 订单二级状态(1已收款，2未收款
 * @property int $freight_state 运费状态（1已收，2未收，4已结）
 * @property int $goods_price_state 代要状态（1财务已收，2未收，4已付）
 * @property int $same_city 是否同城（1同城，2不同城）
 * @property int $abnormal 挂起(1挂起，2正常)
 * @property int $collection 是否代要货款(1代要,2不代要)
 * @property string $collection_poundage_two 代收的手续费2
 * @property int $order_type 订单类型（1线上，2友件）
 * @property int $return_type 退货类型（1返货，2退货）
 * @property int $return_all 是否全返(1:是，2否)
 * @property string $add_time 生成时间(时间戳)
 * @property string $member_name 发货人
 * @property int $member_id 发货人id
 * @property int $member_cityid 发货人市级id
 * @property string $member_phone 发货人电话
 * @property string $receiving_name 收货人
 * @property string $receiving_phone 收货人电话
 * @property string $receiving_name_area 收货人详细地址
 * @property int $receiving_provinceid 收货人省Id
 * @property int $receiving_cityid 收货人市级id
 * @property int $receiving_areaid 收货人区级id
 * @property int $terminus_id 落地点
 * @property int $employee_id 开单员id
 * @property int $shipping_type 运费付款方式
 * @property int $storage_id 入库管理员Id
 * @property int $return_manage_id 退货员Id
 * @property int $scale 落地点运费分成比例
 * @property int $test 是否为测试(1是测试数据，2正常)
 * @property string $shipping_sale 运费优惠价钱
 */
class LogisticsReturnOrderDelete extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_return_order_delete';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'add_time', 'storage_id', 'return_manage_id'], 'required'],
            [['order_id', 'goods_num', 'order_state', 'state', 'freight_state', 'goods_price_state', 'same_city', 'abnormal', 'collection', 'order_type', 'return_type', 'return_all', 'add_time', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'employee_id', 'shipping_type', 'storage_id', 'return_manage_id', 'scale', 'test'], 'integer'],
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_two', 'shipping_sale'], 'number'],
            [['logistics_sn', 'ship_logistics_sn', 'goods_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area'], 'string', 'max' => 255],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'logistics_sn' => 'Logistics Sn',
            'ship_logistics_sn' => 'Ship Logistics Sn',
            'goods_sn' => 'Goods Sn',
            'order_sn' => 'Order Sn',
            'freight' => 'Freight',
            'goods_price' => 'Goods Price',
            'make_from_price' => 'Make From Price',
            'goods_num' => 'Goods Num',
            'order_state' => 'Order State',
            'state' => 'State',
            'freight_state' => 'Freight State',
            'goods_price_state' => 'Goods Price State',
            'same_city' => 'Same City',
            'abnormal' => 'Abnormal',
            'collection' => 'Collection',
            'collection_poundage_two' => 'Collection Poundage Two',
            'order_type' => 'Order Type',
            'return_type' => 'Return Type',
            'return_all' => 'Return All',
            'add_time' => 'Add Time',
            'member_name' => 'Member Name',
            'member_id' => 'Member ID',
            'member_cityid' => 'Member Cityid',
            'member_phone' => 'Member Phone',
            'receiving_name' => 'Receiving Name',
            'receiving_phone' => 'Receiving Phone',
            'receiving_name_area' => 'Receiving Name Area',
            'receiving_provinceid' => 'Receiving Provinceid',
            'receiving_cityid' => 'Receiving Cityid',
            'receiving_areaid' => 'Receiving Areaid',
            'terminus_id' => 'Terminus ID',
            'employee_id' => 'Employee ID',
            'shipping_type' => 'Shipping Type',
            'storage_id' => 'Storage ID',
            'return_manage_id' => 'Return Manage ID',
            'scale' => 'Scale',
            'test' => 'Test',
            'shipping_sale' => 'Shipping Sale',
        ];
    }
}
