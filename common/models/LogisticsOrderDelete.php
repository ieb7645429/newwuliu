<?php

namespace common\models;

use Yii;
use common\models\ShippingTpye;

/**
 * This is the model class for table "logistics_order_delete".
 *
 * @property int $order_id
 * @property string $logistics_sn 物流单号
 * @property string $goods_sn 货号
 * @property string $order_sn 订单编号
 * @property string $freight 运费
 * @property string $goods_price 商品价钱
 * @property string $make_from_price 制单费
 * @property int $goods_num 商品数量
 * @property int $order_state 订单一级状态(0取消，5用户开单，10开单，20分拣，30摆渡车，40货站分拣，50封车，60落地，70送货，80完成)
 * @property int $state 订单二级状态(1买断2不买断,4已收款)
 * @property int $freight_state 运费状态（1已收，2未收，4已结）
 * @property int $goods_price_state 代收状态（1财务已收，2未收，4已付）
 * @property int $abnormal 挂起(1挂起，2正常)
 * @property int $collection 是否代收货款(1代收,2不代收)
 * @property string $collection_poundage_one 代收的手续费1
 * @property string $collection_poundage_two 代收的手续费2
 * @property int $order_type 订单类型（1线上，2友件）有件到物流20，物流到有件21
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
 * @property int $logistics_route_id 物流线路id
 * @property int $shipping_type 运费付款方式
 * @property int $employee_id 开单员id
 * @property int $driver_member_id 司机id
 * @property int $test 是否为测试(1是测试数据，2正常)
 * @property string $shipping_sale 运费优惠价钱
 * @property int $scale 落地点运费分成比例
 * @property int $same_city 是否同城(1是,2不是)
 * @property string $return_logistics_sn 退货票号
 * @property int $delete_member_id 删除人员
 */
class LogisticsOrderDelete extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_order_delete';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_one', 'collection_poundage_two', 'shipping_sale'], 'number'],
            [['goods_num', 'order_state', 'state', 'freight_state', 'goods_price_state', 'abnormal', 'collection', 'order_type', 'add_time', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'logistics_route_id', 'shipping_type', 'employee_id', 'driver_member_id', 'test', 'scale', 'same_city', 'delete_member_id'], 'integer'],
            [['add_time'], 'required'],
            [['logistics_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area', 'return_logistics_sn'], 'string', 'max' => 255],
            [['goods_sn'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'logistics_sn' => '票号',
            'order_sn' => '订单编号',
            'freight' => '运费',
            'goods_price' => '代收款',
            'make_from_price' => '制单费',
            'goods_num' => '数量',
            'order_state' => '状态',
            'state' => '订单二级状态',
            'abnormal' => '挂起',
            'collection' => '代收货款',
            'collection_poundage_one' => '会员费',
            'collection_poundage_two' => '代收手续费',
            'order_type' => '订单类型',
            'add_time' => '生成时间',
            'member_name' => '发货人',
            'member_id' => '发货人id',
            'member_cityid' => '发货人城市',
            'member_phone' => '发货人电话',
            'receiving_name' => '收货人',
            'receiving_phone' => '收货人电话',
            'receiving_name_area' => '收货人详细地址',
            'receiving_provinceid' => '收货人省',
            'receiving_cityid' => '收货人市',
            'receiving_areaid' => '收货人区',
            'terminus_id' => '落地点',
            'logistics_route_id' => '物流线路',
            'shipping_type' => '运费付款方式',
        ];
    }
    
    public function getOrderStateName($orderState,$model = null) {
        switch ($orderState) {
            case Yii::$app->params['orderStateCancel']:
                return '取消';
            case Yii::$app->params['orderStateMember']:
                return '用户下单（未确认）';
            case Yii::$app->params['orderStateEmployee']:
                return '已开单';
            case Yii::$app->params['orderStateDivide']:
                return '物流点已分拣';
            case Yii::$app->params['orderStateFerry']:
                return '已上摆渡车';
            case Yii::$app->params['orderStateDivide2']:
                return '货站已分拣';
            case Yii::$app->params['orderStateDriver']:
                return '已封车';
            case Yii::$app->params['orderStateTerminus']:
                return '已到物流点';
            case Yii::$app->params['orderStateDelivery']:
                if(!empty($model)&&!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn))
                    return '待送货';
                    else
                        return '已送货';
            case Yii::$app->params['orderStateComplete']:
                return '已完成';
        }
    }
    
    
    public function getMemberCityName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'member_cityid']);
    }
    
    public function getUserName()
    {
        return $this->hasOne(User::className(), ['id' => 'member_id']);
    }
    public function getDeleteName()
    {
        return $this->hasOne(User::className(), ['id' => 'delete_member_id']);
    }
    public function getTrueName()
    {
        return $this->hasOne(User::className(), ['id' => 'employee_id']);
    }
    
    public function getReceivingCityName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'receiving_cityid']);
    }
    
    public function getReceivingAreaName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'receiving_areaid']);
    }
    public function idToUserName($id){
        if(!empty(User::findOne($id)->username))
            return User::findOne($id)->username;
            return null;
    }
    
    public function getCollectionName($collection) {
        switch ($collection) {
            case '1':
                return '代收';
            case '2':
                return '不代收';
        }
    }
}
