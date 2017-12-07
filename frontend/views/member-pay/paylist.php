<?php
use yii\grid\GridView;

$this->params['leftmenus'] = $menus;
$this->title = '提现记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apply-for-withdrawal-index">
<?php echo $this->render('_search', ['model' => $applyForWithdrawal,'add_time'=>$add_time]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

//             'id',
//             'user_id',
            [
                'attribute' => 'add_time',
                'contentOptions' => ['style'=>'width:25%;'],
                'value' => function ($model) {
                return date('Y-m-d H:i:s',$model->add_time);
                }
            ],
            'amount',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->getShowStatus($model->status);
                }
            ],
            [
                'label' => '提现订单',
                'attribute' => 'order_sn',
                'value' => function($model){
                    return $model->getWithdrawalOrder($model->id);
                }
            ],
        ],
    ]); ?>
</div>