<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\Area;
use yii\helpers\Url;
use common\models\ShippingTpye;
use common\models\LogisticsOrder;
use backend\assets\Nationwide;


/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrder*/
/* @var $dataProvider yii\data\ActiveDataProvider */




$this->title = '发货单详情';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<div class="logistics-order-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'order_id',
            [
                'attribute' => 'logistics_sn',
                'value' => function($model){
                    if(!empty($model->return_logistics_sn)){
                        return $model->logistics_sn."(已原返)";
                    }
                    return $model->logistics_sn;
                }
            ],
            [
                'attribute' => 'order_sn',
                'value' => function($model){
                    if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
                        return unserialize($model->order_sn);
                    }
                    if(empty($model->order_sn))
                        return '';
                    return $model->order_sn;
                }
            ],
            [
                'label' => '会员号',
                'attribute' => 'userName',
                'value' => function($model) {
                    return $model -> idToUserName($model->member_id);
                },
            ],
            'freight',
            'goods_price',
             'make_from_price',
            'goods_num',
            [
                'attribute' => 'order_state',
                'value' => function($model) {
                    return $model -> getOrderStateName($model->order_state,$model);
                },
                'headerOptions' =>[
                    'width'=>'120px',
                ],
                'filter'=>['5'=>'用户下单','10'=>'已开单','50'=>'已封车','71'=>'待送货','72'=>'已送货']
            ],
            [
                'label' => '垫付',
                'attribute' => 'advance',
                'value' => function($model){
                    return $model->getAdvanceShow($model->order_id);
                },
                'filter'=>['1'=>'已追回','2'=>'已垫付']

            ],
//             'state',
            [
                'attribute' => 'state',
                'value' => function($model) {
                    return $model -> getStateList($model->state);
                },
            ],
//             'abnormal',
            [
                'attribute' => 'abnormal',
                'value' => function($model) {
                    return $model -> getAbnormalList($model->abnormal);
                },
            ],
//             'collection',
            [
                'attribute' => 'collection',
                'value' => function($model) {
                    return $model -> getCollectionList1($model->collection);
                },
            ],
//            'collection_poundage_one',
            [
                'attribute' => 'collection_poundage_one',
                'label' => '代收手续费1',
            ],
//             'collection_poundage_two',
            [
                'attribute' => 'collection_poundage_two',
                'label' => '代收手续费2',
            ],
//             'order_type',
            [
                'attribute' => 'order_type',
                'value' => function($model) {
                    return $model -> getOrderTypeList($model->order_type);
                },
            ],
            // 'add_time',
            [
                'attribute' => 'add_time',
                'label' => '开单时间',
                'options' =>[
                    'width'=>'100px',
                ],
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->add_time);
                }
            ],
            'member_name',
            // 'member_id',
             /*[
                 'attribute' => 'memberCityName.area_name',
                 'label' => '发货人市'
             ],*/
//             'member_cityid',
            [
                'attribute' => 'member_cityid',
                'label' => '发货人市',
                'value' => function($model) {
                    return Area::getAreaNameById($model->member_cityid);
                },
            ],
            'member_phone',
            'receiving_name',
            'receiving_phone',
//             'receiving_name_area',
            /*[
                'label' => '收货人市',
                'attribute' => 'receivingCityName',
                'value' => 'receivingCityName.area_name',
                'contentOptions' => [
                    'width'=>'80'
                ],
            ],*/
            [
                'attribute' => 'receiving_cityid',
                'label' => '收货人市',
                'value' => function($model) {
                    return Area::getAreaNameById($model->receiving_cityid);
                },
            ],
            [
                'label' => '线路',
                'attribute' => 'routeName',
                'value' => function($model) {
                    return \common\models\LogisticsRoute::getLogisticsRouteNameById($model->routeName);
                },
            ],
            /*[
                'label' => '司机',
                'attribute' => 'driverTrueName',
                'value' => 'driverTrueName.user_truename',
            ],*/
            [
                'attribute' => 'driver_member_id',
                'label' => '司机',
                'value' => function($model) {
                    return \common\models\User::getUserNameById($model->driver_member_id);
                },
            ],
           /* [
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],*/
            [
                'attribute' => 'employee_id',
                'label' => '开单员',
                'value' => function($model) {
                    return \common\models\User::getUserNameById($model->employee_id);
                },
            ],
//             [
//                 'label' => '收货人区',
//                 'attribute' => 'receivingAreaName.area_name',
//             ],
//             'receiving_cityid',
//             'receiving_areaid',
//             'terminus_id',
            [

                'attribute' => 'terminus_id',
                'label' => '落地点',
                'value'=>function($model) {
                    return \common\models\Terminus::getNameById($model->terminus_id);
                },
            ],
//             'logistics_route_id',
            [
                'attribute' => 'logistics_route_id',
                'label' => '物流线路',
                'value'=>function($model){
                    return \common\models\LogisticsRoute::getLogisticsRouteNameById($model->logistics_route_id);
                },

            ],
//            'same_city',
            [
                'attribute' => 'same_city',
                'label'=>'是否同城',
                'value' => function($model) {
                    return $model -> getSameCityList($model->same_city);
                },
            ],
//            'shipping_sale',
            [
                'attribute' => 'shipping_sale',
                'label' => '运费优惠价',
            ],
//            'buy_confirm',
            [
                'attribute' => 'buy_confirm',
                'label' => '买家确认',
                'value' => function($model) {
                    return $model -> getBuyConfirmList($model->buy_confirm);
                },
            ],
//            'freight_state',
            [
                'attribute' => 'freight_state',
                'value' => function($model) {
                    return $model -> getFreightStateName($model->freight_state);
                },
            ],
//            'goods_price_state',
            [
                'attribute' => 'goods_price_state',
                'value' => function($model) {
                    return $model -> getGoodsPriceStateName($model->goods_price_state);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '物流价钱生成时间',
                'value' => function($model) {
                    $a= \common\models\OrderTime::getPriceTimeById($model->order_id);
                    return LogisticsOrder::getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '分捡时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getSorterTimeById($model->order_id);
                    return LogisticsOrder::getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '装车时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getRuckTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '卸货时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getUnloadTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '签收时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getSignedForTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '落地点收款时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getCollectionTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '财务收运费时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getIncomeFreightTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '财务付运费时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getPayFreightTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '财务收货款时间',
                'value' => function($model) {
                    $a = \common\models\OrderTime::getIncomePriceTimeById($model->order_id);
                    return LogisticsOrder:: getTableTimeValue($a);
                },
            ],
            [
                'attribute' =>'order_id',
                'label' => '财务付货款时间',
                'value' => function($model) {
                $a = \common\models\OrderTime::getPayPriceTimeById($model->order_id);
                return LogisticsOrder:: getTableTimeValue($a);
                },
            ],

//            'test',
            [
                'attribute' => 'test',
                'label' => '是否为测试',
                'value' => function($model) {
                    return $model -> getTestList($model->test);
                },
            ],


            /*0.0查询、修改、删除按钮*/

            /*[
//                'header' => '查看详情',
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
                'buttons' => [
                    'return' => function ($url, $model, $key) {
                        if(!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn)&&$model->order_state==70){
                            $url = '?r=return/create&order_id='.$model->order_id;
                            return Html::a('原返', $url,['title' => '原返']);
                        }else{
                            return '';
                        }
                    },
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('查看', $url, $options);
                    },
                    'update' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        if($model->order_state==5||$model->order_state==10){
                            return Html::a('修改', $url, $options);
                        }
                    },
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-confirm' => '是否删除订单'.$model->logistics_sn.'?',
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        if(($model->order_state==5||$model->order_state==10)&&$model->employee_id==Yii::$app->user->id&&$model->return_logistics_sn==''){
                            return Html::a('删除', $url, $options);
                        }
                    },
                ]
            ],*/

        ],
    ]); ?>
</div>


