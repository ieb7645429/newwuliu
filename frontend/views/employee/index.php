<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use kartik\daterange\DateRangePicker;
use common\models\LogisticsOrder;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<style>
.form-group{
    width:50%;
}
</style>
<div class="logistics-order-index">

    <hr style="border-top:1px solid #ccc"></hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('发货', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$searchModel,
            'options' => [
                'class' => 'form-control',
                'value' => !empty(Yii::$app->request->get('LogisticsOrderSearch')['add_time']) ? Yii::$app->request->get('LogisticsOrderSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
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
    <?php if(empty($indexOver))$template = '{view} {update} {delete} {return}';else$template = '{view} {return}';?>
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
    	<td><?=empty($count['order_num'])?0:$count['order_num']?></td>
    	<td><?=empty($count['goods_num'])?0:$count['goods_num']?></td>
    	<td><?=empty($count['price_count'])?0:$count['price_count']?></td>
    	<td><?=empty($count['price'])?0:$count['price']?></td>
    	<td><?=empty($count['same_city_order'])?0:$count['same_city_order']?></td>
    	<td><?=empty($count['same_city_goods'])?0:$count['same_city_goods']?></td>
    	<td><?=empty($count['same_city_price_count'])?0:$count['same_city_price_count']?></td>
    	<td><?=empty($count['same_city_price'])?0:$count['same_city_price']?></td>
    </tr>
    </table>
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
                    if(empty($model->order_sn))
                        return '';
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
                'label' => '线路',
                'attribute' => 'routeName',
                'value' => 'routeName.logistics_route_name',
            ],
            [
                'label' => '司机',
                'attribute' => 'driverTrueName',
                'value' => 'driverTrueName.user_truename',
            ],
            [
                'label' => '开单员',
                'attribute' => 'trueName',
                'value' => 'trueName.user_truename',
            ],
//             [
//                 'label' => '收货人区',
//                 'attribute' => 'receivingAreaName.area_name',
//             ],
//             'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',
            [
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
            ],
        ],
        'layout' => "\n{items}\n{pager}",
    ]); ?>
</div>
