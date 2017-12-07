<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsReturnOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-index">

    <hr style="border-top:1px solid #ccc"></hr>
    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加退货单', ['create2'], ['class' => 'btn btn-success']) ?>
    </p>
	<?php if(empty($type))$template = '{view} {update}';else$template = '{view}';?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'logistics_sn',
            'ship_logistics_sn',
//             'goods_sn',
//             'order_sn',
            'freight',
            'goods_price',
//            [
//                'label' => '垫付',
//                'attribute' => 'advance',
//                'value' => function($model){
//                    return $model->getAdvanceShow($model->ship_logistics_sn);
//                },
//                'filter'=>['1'=>'已追回','2'=>'已垫付']
//
//            ],
            [
                'label' => '是否代收',
                'attribute' => 'collection',
                'value' => function($model){
                    return $model->collection == 1 ? '代收' : '不代收';
                },
                'filter' => [1 => '代收', 2 => '不代收'],

            ],
            // 'make_from_price',
            // 'goods_num',
            // 'order_state',
            // 'state',
            // 'abnormal',
            // 'collection',
            // 'collection_poundage_one',
            // 'collection_poundage_two',
            // 'order_type',
            // 'return_type',
            // 'add_time',
            'member_name',
            // 'member_id',
            // 'member_cityid',
            'member_phone',
            'receiving_name',
            'receiving_phone',
            // 'receiving_name_area',
            // 'receiving_provinceid',
            // 'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',
            // 'shipping_type',

            ['class' => 'yii\grid\ActionColumn',
             'template' => $template,
             'buttons' => [
                'update' => function ($url, $model, $key) {
                    if($model->order_state<50){
                        if($model->return_type==1){
                            $url = '?r=hlj/return/update&id='.$model->order_id;
                        }else{
                            $url = '?r=hlj/return/update2&id='.$model->order_id;
                        }
                         return Html::a('修改', $url);
                    }
                 },
				 'view' => function ($url, $model, $key) {
				 if($model->return_type==1){
				     $url = '?r=hlj/return/view&id='.$model->order_id;
				 }else{
				     $url = '?r=hlj/return/view2&id='.$model->order_id;
				 }
                    return Html::a('查看', $url);
                 },
                ]
            ],
            
        ],
    ]); ?>
</div>
