<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use backend\assets\TellerReturnAsset;
TellerReturnAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsReturnOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '退/返货单列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-index">
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(['method'=>'get'])?>
<?= $form->field($searchModel, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
        'convertFormat'=>true,
        'presetDropdown'=>true,
        'model'=>$searchModel,
        'options' => [
            'class' => 'form-control',
            'value' => !empty(Yii::$app->request->get('LogisticsReturnOrderSearch')['add_time']) ? Yii::$app->request->get('LogisticsReturnOrderSearch')['add_time'] : '' ,
        ],
        'pluginOptions'=>[
            'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
        ]
])?>
<?php echo $form->field($searchModel, 'same_city')->label('是否同城')->dropDownList([''=>'','1'=>'同城','2'=>'外阜'])?>
<div class="form-group">
    <?= Html::submitButton('查询', ['class' => 'btn btn-primary','id'=>'searchButton']) ?>
</div>
<?php ActiveForm::end(); ?>



    <table class="table">
    <tr>
    	<th>票数</th>
    	<th>代收款</th>
    	<th>运费</th>
    </tr>
    <tr>
    	<td><?= empty($count['order_num'])?0:$count['order_num']?></td>
    	<td><?= empty($count['order_price'])?0:$count['order_price']?></td>
    	<td><?= empty($count['freight'])?0:$count['freight']?></td>
    </tr>
    </table>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'logistics_sn',
            'ship_logistics_sn',
            [
                'label' => '订单编号',
                'value' => function($model) {
                    return $model->getLogisticsOrderSn();
                 },
            ],
//             'goods_sn',
//             'order_sn',
//             'freight',
//             'goods_price',
//             [
//                 'label' => '垫付',
//                 'attribute' => 'advance',
//                 'value' => function($model){
//                     return $model->getAdvanceShow($model->ship_logistics_sn);
//                 },
//                 'filter'=>['1'=>'已追回','2'=>'已垫付']
            
//             ],
//                 [
//                     'label' => '订单类型',
//                     'attribute' => 'order_type',
//                     'value' => function($model){
//                     switch ($model->order_type)
//                     {
//                         case '1':
//                             return '西部';
//                             break;
//                         case '3':
//                             return '瑞胜';
//                             break;
//                         case '4':
//                             return '塔湾';
//                             break;
//                         default:
//                             return '无';
//                             break;
//                     }
//                     },
//                     'filter'=>['1'=>'西部','3'=>'瑞胜','4'=>'塔湾']
//                 ],
//                 [
//                 'attribute' => 'order_state',
//                 'label' => '订单状态',
//                 'value' => function($model, $key, $index, $column) {
//                     switch ($model->order_state)
//                     {
//                         case '10':
//                             return '已开单';
//                             break;
//                         case '20':
//                             return '待分拨';
//                             break;
//                         case '30':
//                             return '待入库';
//                             break;
//                         case '50':
//                             return '待送货';
//                             break;
//                         case '70':
//                             return '已收款';
//                             break;
//                         default:
//                             return 0;
//                     }
//                 },
//                 'headerOptions' =>[
//                         'width'=>'100px',
//                 ],
//                 'filter'=>['10'=>'已开单','20'=>'待分拨','30'=>'待入库','50'=>'待送货','70'=>'已收款']
//             ],
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
//             [
//                 'label'=>'开单时间',
//                 'attribute'=>'add_time',
//                 'value'=>function($model){
//                     return date('Y-m-d H:i:s',$model->add_time);
//                  }
//             ],
//             [
//                 'label' => '开单员',
//                 'attribute' => 'trueName',
//                 'value' => 'trueName.user_truename',
//             ],
//             [
//                 'label' => '送货员',
//                 'attribute' => 'senderName',
//                 'value' => 'senderName.sender',
//             ],
            [
                'label' => '状态',
                'attribute' => 'goods_price_state',
                'contentOptions' => ['class'=>'goods_price_state'],
                'value' => function ($model) {
                    return $model->getGoodsPriceStateName($model->goods_price_state);
                },
                'filter'=>['1'=>'已收款','2'=>'未收款','4'=>'已返款']
            ],
            [
                'label' => '入库时间',
                'value' => function ($model) {
                    if($model->returnOrderTime->ruck_time) {
                        return date('Y-m-d H:i', $model->returnOrderTime->ruck_time);
                    }
                    return '';
                },
            ],
            [
                'label' => '收款时间',
                'contentOptions' => ['class'=>'income_time'],
                'value' => function ($model) {
                    if($model->returnOrderTime->income_price_time) {
                        return date('Y-m-d H:i', $model->returnOrderTime->income_price_time);
                    }
                    return '';
                },
            ],
            [
                'label' => '返款时间',
                'contentOptions' => ['class'=>'pay_time'],
                'value' => function ($model) {
                    if($model->returnOrderTime->pay_price_time) {
                        return date('Y-m-d H:i', $model->returnOrderTime->pay_price_time);
                    }
                    return '';
                },
            ],
            // 'receiving_name_area',
            // 'receiving_provinceid',
            // 'receiving_cityid',
            // 'receiving_areaid',
            // 'terminus_id',
            // 'logistics_route_id',
            // 'shipping_type',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{income} {pay} ',
                'buttons' => [
                    'income' => function ($url, $model, $key) {
                        return Html::button('收款', ['id'=>'confirm-income_'.$key,'class' => 'btn btn-danger confirm-income','data-order-id'=>$key]);
                    },
                    'pay' => function ($url, $model, $key) {
                        return Html::button('返款', ['id'=>'confirm-pay_'.$key,'class' => 'btn btn-warning confirm-pay','data-order-id'=>$key]);
                    },
                ],
                'visibleButtons' => [
                    'income' => function ($model, $key, $index) {
                        if($model->same_city == 1) {
                            if($model->order_state >= 10 && $model->goods_price_state == 2) {
                                return true;
                            }
                            return false;
                        }
                        if($model->same_city == 2) {
                            if($model->order_state >= 50 && $model->goods_price_state == 2) {
                                return true;
                            }
                            return false;
                        }
                    },
                    'pay' => function ($model, $key, $index) {
                        if($model->same_city == 1) {
                            if($model->order_state >= 10 && ($model->goods_price_state & 1) && !($model->goods_price_state & 4)) {
                                return true;
                            }
                            return false;
                        }
                        if($model->same_city == 2) {
                            if($model->order_state >= 50 && ($model->goods_price_state & 1) && !($model->goods_price_state & 4)) {
                                return true;
                            }
                            return false;
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>
<?php Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">填写送货人</h4>',
]);
?>
<div class="payDiv">
    <div class="payInput">
    	<?=Html::input('text','sender','',['id'=>'sender','class' => 'form-control pay-input']);?>
    	<?=Html::input('hidden','order_id','',['id'=>'order_id','class' => 'form-control pay-input']);?>
    </div>
    <div class="row payButtonDiv">
    	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary']);?></div>
    	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
    </div>
</div>
<?php Modal::end();?>
