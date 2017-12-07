<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
$this->params['leftmenus'] = $menus;
$this->title = '账单明细';
$this->params['breadcrumbs'][] = $this->title;
Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">订单详情</h4>',
]);

Modal::end();
?>
<div class="apply-for-withdrawal-index">
<?php echo $this->render('_search', ['model' => $withdrawalLog,'add_time'=>$add_time]); ?>
<div style="height:30px;line-height: 30px">可提现余额:<?=$withdrawal_amount?></div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
             [
                   'attribute' => 'order_sn',
                   'value' => function($model){
                        if(empty($model->order_sn)){
                            return $model->OutOrderSn($model->uid,$model->add_time);
                        }else{
                            return $model->order_sn;
                        }
                    },
                   'headerOptions' => ['style'=>'width:25%;'],
               ],
//                [
//                    'label' => '订单编号',
//                    'attribute' => 'orderSn',
//                    'value' => function($model){
//                         if(!empty($model->orderSn->order_sn)){
//                             if(!is_numeric($model->orderSn->order_sn)){
//                                  return unserialize($model->orderSn->order_sn);
//                             }
//                             return $model->orderSn->order_sn;
//                         }
//                         return '';
//                     },
//                     'headerOptions' => ['style'=>'width:25%;'],
//                ],
//             'id',
//             'uid',
              [
                    'attribute' => 'amount', 
                    'value' => function($model){
                        return $model->getViewAmount($model->type).$model->amount;
                    }
                ],
                [
                'attribute' => 'add_time',
                'value' => function ($model) {
                return date('Y-m-d H:i:s',$model->add_time);
                }
                ],
                [
                    'label' => '操作前金额',
                    'attribute' => 'before_amount'
                ],
                [
                    'label' => '操作后金额',
                    'attribute' => 'after_amount'
                ],
                // 'content:ntext',
                [
                    'label' => '操作',
                    'attribute' => 'type',
                    'value' => function($model){
                        return $model->getType($model->type);
                    }
                ],
                [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                        'view' => function ($url, $model, $key) {
                            $data = [
                                'data-id' => $model->id,
                                'data-toggle' => 'modal',
                                'data-target' => '#create-modal',
                                'class' => 'order_details',
                            ];
                            return Html::a('查看', 'javascript:;',$data);
                        },
                    ]
                ],
        ],
    ]); ?>
</div>
<?php 
$js = <<<JS
    $(".order_details").click(function(){ 
        $.ajax({
            type: 'post',
            url:'?r=member-pay/ajax-order-details',
            data:{'id':$(this).data('id')},
            async:true,
            success:function(data){
        console.log(data);
                $('.modal-body').html(data);
            }
       })
    }); 
JS;
$this->registerJs($js);
?>