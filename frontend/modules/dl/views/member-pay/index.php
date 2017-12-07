<?php
use yii\grid\GridView;
use frontend\modules\dl\assets\MemberPayableAsset;
MemberPayableAsset::register($this);

$this->title = '已提现订单';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="withdrawal-log-index">
<?php echo $this->render('_overSearch', ['model' => $withdrawalLog,'withdrawal_time'=>$add_time]); ?>
<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
             [
                   'attribute' => 'order_sn',
                   'value' => function($model){
                        return empty($model->order_sn)?'':$model->order_sn;
                    },
                   'headerOptions' => ['style'=>'width:25%;'],
                   'contentOptions' => ['style'=>'width:25%;'],
               ],
               [
                   'label' => '订单编号',
                   'attribute' => 'orderSn',
                   'headerOptions' => ['style'=>'width:25%;'],
                   'value' => function($model){
                        if(!empty($model->orderSn->order_sn)){
                            if(!is_numeric($model->orderSn->order_sn)){
                                 return unserialize($model->orderSn->order_sn);
                            }
                            return $model->orderSn->order_sn;
                        }
                        return '';
                    }
               ],
              
//             'id',
//             'uid',
              [
                    'attribute' => 'amount', 
                    'value' => function($model){
                        return $model->getViewAmount($model->type).$model->amount;
                    }
                ],
                [
                'attribute' => 'addTime',
                'label' => '提现时间',
                'value' => function ($model) {
                return $model->getWithdrawalTime($model->order_sn);
                }
                ],
//             [
//                 'label' => '操作前金额',
//                 'attribute' => 'before_amount'
//             ],
//             [
//                 'label' => '操作后金额',
//                 'attribute' => 'after_amount'
//             ],
//             // 'content:ntext',
//             [
//                 'label' => '操作',
//                 'attribute' => 'type',
//                 'value' => function($model){
//                     return $model->getType($model->type);
//                 }
//             ],
        ],
    ]); ?>
</div>