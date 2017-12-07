<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BalanceLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '可提现金额记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="balance-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'user.user_truename',
                'label' => '用户'
            ],
            [
                'attribute' => 'amount',
                'value' => function($model){
                    return $model->getViewAmount($model->type).$model->amount;
                }
            ],
            'before_amount',
            'after_amount',
            'content:ntext',
//             'type',
//             'source_type',
            'order_sn',
            [
                'label' => '友件订单号',
                'value' => function($model) {
                if(!empty($model->lorder_sn)&&!is_numeric($model->lorder_sn)){
                    return unserialize($model->lorder_sn);
                }
                return $model->lorder_sn;
            }
            ],
            [
                'attribute' => 'add_time',
                'format' => ['date', "php:Y-m-d H:i:s"],
            ],
        ],
    ]); ?>
</div>
