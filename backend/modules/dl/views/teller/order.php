<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use backend\modules\dl\assets\TellerOrderAsset;

TellerOrderAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<style>
.form-group{
    width:400px;
}
</style>
<div class="logistics-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$searchModel,
            'options' => [
                'class' => 'form-control',
                'value' => !empty(Yii::$app->request->get('LogisticsOrderSearch')['add_time']) ? Yii::$app->request->get('LogisticsOrderSearch')['add_time'] : '' ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
   ])?>
   <?= Html::hiddenInput('download_type', '0', ['id' => 'download_type']) ?>
   <div class="form-group">
        <?= Html::Button('查询', ['class' => 'btn btn-primary', 'id' => 'searchButton']) ?>
        <?php echo Html::Button('导出Excel', ['class' => 'btn btn-warning', 'id' => 'downloadExcel']); ?>
    </div>
	<table class="table">
    <tr>
    	<th>票数</th>
    	<th>件数</th>
    	<th>代收票数</th>
    	<th>代收总金额</th>
    	<th>同城票数</th>
    	<th>同城件数</th>
    	<th>同城代收票数</th>
    	<th>同城代收总金额</th>
    </tr>
    <tr>
    	<td><?=$count['order_num']?></td>
    	<td><?=$count['goods_num']?></td>
    	<td><?=$count['price_count']?></td>
    	<td><?=$count['price']?></td>
    	<td><?=$count['same_city_order']?></td>
    	<td><?=$count['same_city_goods']?></td>
    	<td><?=$count['same_city_price_count']?></td>
    	<td><?=$count['same_city_price']?></td>
    </tr>
    </table>
    <?php ActiveForm::end(); ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
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
                    return $model->order_sn;
                }
            ],
            [
                'label' => '会员号',
                'attribute' => 'userName',
                'value' => 'userName.username',
            ],
            'freight',
            'goods_price',
            // 'make_from_price',
            'goods_num',
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
            [
                'label' => '垫付',
                'attribute' => 'advance',
                'value' => function($model){
                    return $model->getAdvanceShow($model->order_id);
                },
                'filter'=>['1'=>'已追回','2'=>'已垫付']
                
            ],
            // 'state',
            // 'abnormal',
//             'collection',
            // 'collection_poundage_one',
            // 'collection_poundage_two',
            // 'order_type',
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
//             [
//                 'attribute' => 'memberCityName.area_name',
//                 'label' => '发货人城市'
//             ],
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
            'receiving_phone',
//             'receiving_name_area',
            [
                'label' => '收货人市',
                'attribute' => 'receivingCityName',
                'value' => 'receivingCityName.area_name',
            		'contentOptions' => [
            				'width'=>'80'
            		],
            ],
            [
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],
            [
                'attribute' => 'driver_member_id',
                'value' => function($model) {
                    return $model->getDriverName();
                },
                'filter'=>$driverList
            ],
            [
                'attribute' => 'freight_state',
                'value' => function($model) {
                    return $model->getFreightStateName($model->freight_state);
                },
                'filter'=>[1=>'已收款',2=>'未收款',4=>'已结款']
            ],
            [
                'attribute' => 'goods_price_state',
                'value' => function($model) {
                    return $model->getGoodsPriceStateName($model->goods_price_state);
                },
                'filter'=>[1=>'已收款',2=>'未收款']
            ],
//             [
//                 'label' => '收货人区',
//                 'attribute' => 'receivingAreaName.area_name',
//             ],
//             'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',
        ],
    ]); ?>
</div>
