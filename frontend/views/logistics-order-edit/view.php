<?php

use yii\helpers\Html;
use common\models\Area;
use yii\widgets\DetailView;
use common\models\LogisticsOrderEdit;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderEdit */

$this->title = '订单详情';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-edit-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--    <p>
        <?/*= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) */?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>-->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
//            'order_id',
            'logistics_sn',
            'goods_sn',
//            'order_sn',
            [
                'attribute' => 'order_sn',
                'value' => function ($model) {
                    if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
                        return unserialize($model->order_sn);
                    }
                    return $model->order_sn;
                }
            ],
            'freight',
            'goods_price',
            'make_from_price',
            'goods_num',
//            'order_state',
            [
                'attribute' => 'order_state',
                'value' => function($model) {
                    return $model -> getOrderStateName($model->order_state,$model);
                },
            ],
//            'state',
            [
                'attribute' => 'state',
                'value' => function($model) {
                    return $model -> getStateList($model->state);
                },
            ],
//            'freight_state',
            [
                'attribute' => 'freight_state',
                'value' => function($model) {
                    return $model -> getFreightStateList($model->freight_state);
                },
            ],
//            'goods_price_state',
            [
                'attribute' => 'goods_price_state',
                'value' => function($model) {
                    return $model -> getGoodsPriceStateList($model->goods_price_state);
                },
            ],
//            'abnormal',
            [
                'attribute' => 'abnormal',
                'value' => function($model) {
                    return $model -> getAbnormalList($model->abnormal);
                },
            ],
//            'collection',
            [
                'attribute' => 'collection',
                'value' => function($model) {
                    return $model -> getCollectionList($model->collection);
                },
            ],
            'collection_poundage_one',
            'collection_poundage_two',
//            'order_type',
            [
                'attribute' => 'order_type',
                'value' => function($model) {
                    return $model -> getOrderTypeList($model->order_type);
                },
            ],
//            'add_time',
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
//            'member_cityid',
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
            'receiving_name_area',
//            'receiving_provinceid',
//            'receiving_cityid',
            [
                'attribute' => 'receiving_cityid',
                'label' => '收货人市',
                'value' => function($model) {
                    return Area::getAreaNameById($model->receiving_cityid);
                },
            ],
//            'receiving_areaid',
//            'terminus_id',
            [

                'attribute' => 'terminus_id',
                'label' => '落地点',
                'value'=>function($model) {
                        return \common\models\Terminus::getNameById($model->terminus_id);
                },
            ],
//            'logistics_route_id',
        [
            'attribute' => 'logistics_route_id',
            'label' => '物流线路',
            'value'=>function($model){
                    return \common\models\LogisticsRoute::getLogisticsRouteNameById($model->logistics_route_id);
            },

        ],
//            'shipping_type',
            [
                'attribute' => 'shipping_type',
                'label' => '运费付款方式',
                'value' => function($model) {
                    return $model -> getShippingTypeName($model->shipping_type);
                },
            ],
//            'employee_id',
            [
                'attribute' => 'employee_id',
                'label' => '开单员',
                'value' => function($model) {
                        return \common\models\User::getUserNameById($model->employee_id);
                },
            ],
//            'driver_member_id',
            [
                'attribute' => 'driver_member_id',
                'label' => '司机',
                'value' => function($model) {
                    return \common\models\User::getUserNameById($model->driver_member_id);
                },
            ],
//            'test',
            [
                'attribute' => 'test',
                'value' => function($model) {
                    return $model -> getTestList($model->test);
                },
            ],
            'shipping_sale',

            'scale',
//            'same_city',
            [
                'attribute' => 'same_city',
                'value' => function($model) {
                    return $model -> getSameCityList($model->same_city);
                },
            ],
            'return_logistics_sn',
//            'edit_time',
            [
                'attribute' => 'edit_time',
                'label' => '修改时间',
                'options' =>[
                    'width'=>'100px',
                ],
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->edit_time);
                }
            ],
//            'edit_member_id',
            [
                'attribute' => 'edit_member_id',
                'label' => '修改操作员',
                'value' => function($model) {
                    return \common\models\User::getUserNameById($model->edit_member_id);
                },
            ],
        ],
    ]) ?>

</div>
