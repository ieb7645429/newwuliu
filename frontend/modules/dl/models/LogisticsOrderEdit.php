<?php

namespace frontend\modules\dl\models;

use Yii;
use yii\base\Exception;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use backend\modules\dl\models\OrderRemark;

/**
 * This is the model class for table "logistics_order_edit".
 *
 * @property int $id
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
 * @property int $order_type 订单类型（1线上，3瑞胜）有件到物流20，物流到有件21
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
 * @property string $edit_time 修改时间
 * @property int $edit_member_id 修改操作员
 */
class LogisticsOrderEdit extends \yii\db\ActiveRecord
{
    const SCENARIO_EDIT = 'edit';
    
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_EDIT] = [];
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logistics_order_edit';
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
            [['order_id', 'goods_num', 'order_state', 'state', 'freight_state', 'goods_price_state', 'abnormal', 'collection', 'order_type', 'add_time', 'member_id', 'member_cityid', 'receiving_provinceid', 'receiving_cityid', 'receiving_areaid', 'terminus_id', 'logistics_route_id', 'shipping_type', 'employee_id', 'driver_member_id', 'test', 'scale', 'same_city', 'edit_member_id'], 'integer'],
            [['freight', 'goods_price', 'make_from_price', 'collection_poundage_one', 'collection_poundage_two', 'shipping_sale'], 'number'],
            [['add_time'], 'required'],
            [['logistics_sn', 'order_sn', 'member_name', 'member_phone', 'receiving_name', 'receiving_phone', 'receiving_name_area', 'return_logistics_sn', 'edit_time'], 'string', 'max' => 255],
            [['goods_sn'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
//            'id' => 'ID',
            'order_id' => 'Order ID',
            'logistics_sn' => '票号',
            'goods_sn' => '货号',
            'order_sn' => '订单编号',
            'freight' => '运费',
            'goods_price' => '代收款',
            'make_from_price' => '制单费',
            'goods_num' => '数量',
            'order_state' => '状态',
            'state' => '订单二级状态',
            'freight_state' => '运费状态',
            'goods_price_state' => '代收状态',
            'abnormal' => '挂起',
            'collection' => '是否代收货款',
            'collection_poundage_one' => '代收的手续费1',
            'collection_poundage_two' => '代收的手续费2',
            'order_type' => '订单类型',
            'add_time' => '生成时间',
            'member_name' => '发货人',
            'member_id' => '发货人id',
            'member_cityid' => '发货人市级id',
            'member_phone' => '发货人电话',
            'receiving_name' => '收货人',
            'receiving_phone' => '收货人电话',
            'receiving_name_area' => '收货人详细地址',
            'receiving_provinceid' => '收货人省Id',
            'receiving_cityid' => '收货人市级id',
            'receiving_areaid' => '收货人区级id',
            'terminus_id' => '落地点',
            'logistics_route_id' => '物流线路id',
            'shipping_type' => '运费付款方式',
            'employee_id' => '开单员id',
            'driver_member_id' => '司机id',
            'test' => '是否为测试',
            'shipping_sale' => '运费优惠价钱',
            'scale' => '落地点运费分成比例',
            'same_city' => '是否同城',
            'return_logistics_sn' => '退货票号',
            'edit_time' => '修改时间',
            'edit_member_id' => '修改操作员',
        ];
    }
    
    
    /**
     * 靳健
     * 订单修改记录log
     * @param unknown $model
     */
    public function addOrderEditLog($model,$param){
        $order_remark = new OrderRemark();
        $remark_model = $order_remark::findOne($model->order_id);
        if($this->isOrderEdit($model,$remark_model,$param)){//保存信息有变动
            $model_edit = $this::findOne(['order_id'=>$model->order_id]);
            if(empty($model_edit)){//添加订单原始信息
                $this->addOrderEdit($model,$remark_model,$param,'old');
            }
            $this->addOrderEdit($model,$remark_model,$param);
        }
    }
    
    /**
     * 靳健
     * 添加订单修改记录
     * @param unknown $model
     * @param unknown $type
     */
    public function addOrderEdit($model,$remark_model,$param,$type = null){
        $edit_model = new LogisticsOrderEdit();
        $orderRemark = new OrderRemark();
        $edit_model->scenario = 'edit';
        if($type=='old'){
            foreach($model as $key=>$value){
                if($edit_model->hasProperty($key,true))
                    $edit_model->$key = $model->getOldAttribute($key);
            }
            $edit_model->edit_time = $model->add_time;
            $edit_model->order_remark = !empty($remark_model)?$remark_model->edit_content:'';
        }else{
            foreach($model as $key=>$value){
                if($edit_model->hasProperty($key,true))
                    $edit_model->$key = $value;
            }
            $edit_model->edit_time = time();
            $edit_model->order_remark = !empty($remark_model)?$param['edit_content']:'';
        }
        $edit_model->edit_member_id = Yii::$app->user->id;
        $edit_model->save();
    }
    
    /**
     * 靳健
     * 判断订单信息是否有修改内容
     */
    public function isOrderEdit($model,$remark_model,$param){
        $order_remark = new OrderRemark();
        foreach($model as $key => $value){
            if($model->$key != $model->getOldAttribute($key)) return true;
        }
        if(empty($remark_model)){
            if(!empty($param['edit_content'])) return true;
        }else{
            if($remark_model->edit_content!=$param['edit_content']) return true;
        }
        
        return false;
    }




    /*
     * 0.0
     * 状态OrderState
    */
    public function getOrderStateName($orderState,$model = null)
    {
        switch ($orderState)
        {
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
//                 if(!empty($model)&&!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn))
                if(!empty($model)&&($model->state==1||$model->state==2)&&$model->freight_state==2&&empty($model->return_logistics_sn))
                    return '待送货';
                else
                    return '已送货';
            case Yii::$app->params['orderStateComplete']:
                return '已完成';
        }
    }


    /*
     * 0.0
     * 运费状态FreightState
    */
    public static function getFreightStateList($FreightState)
    {
        switch ($FreightState)
        {
            case '1':
                return '已收';
            case '2':
                return '未收';
            case '3':
                return '已结';
        }
    }

    /*
     * 0.0
     * 代收状态GoodsPriceState
   */

    public static function getGoodsPriceStateList($GoodsPriceState)
    {
        switch ($GoodsPriceState)
        {
            case '1':
                return '财务已收';
            case '2':
                return '未收';
            case '3':
                return '已付';
        }
    }

    /*0.0
    *是否为测试
   */
    public static function getTestList($GoodsPriceState)
    {
        switch ($GoodsPriceState)
        {
            case '1':
                return '测试数据';
            case '2':
                return '正常';
        }
    }

    /*0.0
    *是否同城  SameCity
   */
    public static function getSameCityList($GoodsPriceState)
    {
        switch ($GoodsPriceState)
        {
            case '1':
                return '是';
            case '2':
                return '不是';
        }
    }

    /*0.0
    *挂起  Abnormal
   */
    public static function getAbnormalList($Abnormal)
    {
        switch ($Abnormal)
        {
            case '1':
                return '挂起';
            case '2':
                return '正常';
        }
    }

    /*0.0
    *订单二级状态  State
   */
    public static function getStateList($State)
    {
        switch ($State)
        {
            case '1':
                return '买断';
            case '2':
                return '不买断';
            case '3':
                return '已收款';
        }
    }


    /*0.0
    *是否代收货款  Collection
   */
    public static function getCollectionList($Collection)
    {
        switch ($Collection)
        {
            case '1':
                return '代收';
            case '2':
                return '不代收';
        }
    }


    /*0.0
    *订单类型  OrderType
   */
    public static function getOrderTypeList($OrderType)
    {
        switch ($OrderType)
        {
            case '1':
                return '线上';
            case '3':
                return '瑞胜';
        }
    }


    /*
     * 0.0
     * 得到 运费付款方式  对应 姓名 ShippingType
   */
    public function getShippingTypeName($ShippingType)
    {
        switch ($ShippingType)
        {
            case '1':
                return '提付';
            case '2':
                return '回付';
            case '3':
                return '已付';
        }
    }

    /*
     * 0.0
     * 查询得到 城市表 中的 发货人 MemberCityName
   */
    public function getMemberCityName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'member_cityid']);
    }

    /*
     * 0.0
     * 查询得到 城市表 中的 收货人 ReceivingCityName
   */
    public function getReceivingCityName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'receiving_cityid']);
    }

    /*
     * 0.0
     * 查询得到 城市表 中的 收货人 ReceivingProvinceName
   */
    public function getReceivingProvinceName()
    {
        return $this->hasOne(Area::className(), ['area_id' => 'receiving_cityid']);
    }

    /*
     * 0.0
     * 得到开单员id 对应 姓名 TrueName
   */

    public function getTrueName()
    {
        return $this->hasOne(User::className(), ['id' => 'employee_id']);
    }

    /*
     * 0.0
     * 得到司机id 对应 姓名 DriverTrueName
   */
    public function getDriverTrueName()
    {
        return $this->hasOne(User::className(), ['id' => 'driver_member_id']);
    }

    /*
     * 0.0
     * 得到 修改操作员 id 对应 姓名 OperatorName
   */
    public function getOperatorName()
    {
        return $this->hasOne(User::className(), ['id' => 'edit_member_id']);
    }



}
