<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\Area;
use yii\helpers\Url;
use common\models\LogisticsOrderEdit;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderEditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单显示';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<style>
    .form-group{
        width:50%;
    }
</style>

<div class="logistics-order-edit-index">

    <h1><?= Html::encode($this->title) ?></h1>
      <hr style="border-top:1px solid #ccc"></hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
<!--  <?//= Html::a('Create Logistics Order Edit', ['create'], ['class' => 'btn btn-success']) ?>      -->
    </p>

    <?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
        'convertFormat'=>true,
        'presetDropdown'=>true,
        'model'=>$searchModel,
        'options' => [
            'class' => 'form-control',
            'value' => !empty(Yii::$app->request->get('LogisticsOrderEditSearch')['add_time']) ? Yii::$app->request->get('LogisticsOrderEditSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
        ],
        'pluginOptions'=>[
            'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
        ]
    ])?>
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?php // echo Html::resetButton('重置', ['class' => 'btn btn-default']); ?>
    </div>
    <?php //echo '商品数量:'.$goods_num.'件  今日订单:'.($orderNum['1']['orderNum']+$orderNum['2']['orderNum']).'  今日同城订单:'.$orderNum['1']['orderNum'].'  代收款订单数 :'.$allPrice['count'].'件   代收款金额:'.$allPrice['price'];?>
    <?php ActiveForm::end(); ?>

    <?php if(empty($indexOver))$template = '{view}';else$template = '{view}';?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'order_id',
            [
                'attribute' => 'logistics_sn',
                'value' => function($model){
                    if(!empty($model->return_logistics_sn)){
                        return $model->logistics_sn."(已原返)";
                    }
                    return $model->logistics_sn;
                }
            ],
//            'logistics_sn',
            /*[
                'attribute' => 'order_sn',
                'value' => function($model){
                    if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
                        return unserialize($model->order_sn);
                    }
                    if(empty($model->order_sn))
                        return '';
                    return $model->order_sn;
                },
            ],*/
//            'goods_sn',
//            'order_sn',
             'freight',
             'goods_price',
//             'make_from_price',
             'goods_num',
//             'order_state',
            [
                'attribute' => 'order_state',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getOrderStateName($model->order_state,$model);
                },
                'headerOptions' =>[
                    'width'=>'120px',
                ],
                'filter'=>['5'=>'用户下单','10'=>'已开单','50'=>'已封车','71'=>'待送货','72'=>'已送货']
            ],
//             'state',
            /*[
                'attribute' => 'state',
                'value' => function($model) {
                    return $model -> getStateList($model->state);
                },
                'filter'=>['1'=>'买断','2'=>'不买断','4'=>'已收款'],
            ],*/
//             'freight_state',
        /*[
            'attribute' => 'freight_state',
            'value' => function($model, $key, $index, $column) {
                return $model -> getFreightStateList($model->freight_state);
            },
            'filter'=>['1'=>'已收','2'=>'未收','4'=>'已结'],
        ],*/
//             'goods_price_state',
        /*[
            'attribute' => 'goods_price_state',
            'value' => function($model, $key, $index, $column) {
                return $model -> getGoodsPriceStateList($model->goods_price_state);
            },
            'filter'=>['1'=>'财务已收','2'=>'未收','4'=>'已付'],
        ],*/
//             'abnormal',
            /*[
                'attribute' => 'abnormal',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getAbnormalList($model->abnormal);
                },
                'filter'=>['1'=>'挂起','2'=>'正常'],
            ],*/
//             'collection',
//             'collection_poundage_one',
//             'collection_poundage_two',
//             'order_type',
//             'add_time',
            /*[
                'attribute' => 'add_time',
                'label' => '开单时间',
                'options' =>[
                    'width'=>'100px',
                ],
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->add_time);
                }
            ],*/
//             'member_name',
//             'member_id',
//             'member_cityid',
            [
                'label' => '发货人市',
                'attribute' => 'memberCityName',
                'value' => 'memberCityName.area_name',
                'contentOptions' => [
                    'width'=>'80'
                ],
            ],
             'member_phone',
             'receiving_name',
//             'receiving_name_area',
            [
                'label' => '收货人市',
                'attribute' => 'receivingCityName',
                'value' => 'receivingCityName.area_name',
                'contentOptions' => [
                    'width'=>'80'
                ],
            ],
            'receiving_phone',
//             'receiving_provinceid',
           /*[
                'label' => '收货人省',
                'attribute' => 'receivingProvinceName',
                'value' => 'receivingProvinceName.area_name',
                'contentOptions' => [
                    'width'=>'80'
                ],
            ],*/
//             'receiving_cityid',
//             'receiving_areaid',
//             'terminus_id',
//             'logistics_route_id',

//             'shipping_type',
            /*[
                'attribute' => 'shipping_type',
                'label' => '运费付款方式',
                'value' => function($model) {
                    return \common\models\ShippingTpye::getShippingTypeNameById($model->shipping_type);
                },
            ],*/
            [
                'attribute' => 'shipping_type',
                'label' => '运费付款方式',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getShippingTypeName($model->shipping_type);
                },
                'filter'=>['1'=>'提付','3'=>'已付'],
            ],

//             'employee_id',
            /*[
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],*/
//             'driver_member_id',
            [
                'label' => '司机',
                'attribute' => 'driverTrueName',
                'value' => 'driverTrueName.user_truename',
            ],

//             'test',
            /*[
                'attribute' => 'test',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getTestList($model->test);
                },
                'filter'=>['1'=>'测试数据','2'=>'正常'],
            ],*/
//             'shipping_sale',
//             'scale',
//             'same_city',
           /* [
                'attribute' => 'same_city',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getSameCityList($model->same_city);
                },
                'filter'=>['1'=>'是','2'=>'不是'],
            ],*/
//             'return_logistics_sn',
//             'edit_time',
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
//             'order_remark:ntext',
//
//             'edit_member_id',
//            operatorName
        [
            'label' => '修改操作员',
            'attribute' => 'operatorName',
            'value' => 'operatorName.user_truename',
        ],


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => $template,
                'buttons' => [

                   /* 'return' => function ($url, $model, $key) {
                        if(!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn)&&$model->order_state==70){
                            $url = '?r=return/create&order_id='.$model->order_id;
                            return Html::a('原返', $url,['title' => '原返']);
                        }else{
                            return '';
                        }
                    },*/
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('查看', $url, $options);
                    },

                    /*'update' => function ($url, $model, $key) {
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
                    },*/
                ]

            ],
        ],
        'layout' => "\n{items}\n{pager}",
    ]); ?>
</div>
