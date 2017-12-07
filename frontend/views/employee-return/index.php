<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '查看';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <hr style="border-top:1px solid #ccc"></hr>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php if(empty($indexOver)){
             $template = '{view} {update}';
        }elseif($indexOver=='over'){
             $template = '{view} {return}';
        }elseif($indexOver=='return'){
            $template = '{view}';
        }
    ?>
    <?php //if(empty($indexOver))$template = '{view} {update}';else$template = '{return}';?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'order_id',
            'logistics_sn',
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
                'contentOptions' => [
                        'width'=>'80'
                ],
            ],
            'freight',
            'goods_price',
            // 'make_from_price',
            'goods_num',
            [
                'attribute' => 'order_state',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getOrderStateName($model->order_state);
                },
            ],
            // 'state',
            // 'abnormal',
//             'collection',
            // 'collection_poundage_one',
            // 'collection_poundage_two',
            // 'order_type',
            // 'add_time',
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
//             'member_phone',
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
                        if(!($model->state&4)&&$model->collection==1&&empty($model->return_logistics_sn)){
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
                    return Html::a('修改', $url, $options);
                 },
//                  'delete' => function ($url, $model, $key) {
//                      $options = [
//                              'title' => Yii::t('yii', 'View'),
//                              'aria-label' => Yii::t('yii', 'View'),
//                              'data-confirm' => '是否删除订单?',
//                              'data-method' => 'post',
//                              'data-pjax' => '0',
//                      ];
//                  return Html::a('删除', $url, $options);
//                  },
                ]
            ],
        ],
    ]); ?>
</div>
