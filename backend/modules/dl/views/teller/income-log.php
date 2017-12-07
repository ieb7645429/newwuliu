<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TellerLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收款明细';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="teller-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search_log', ['model' => $searchModel, 'userList' => $userList]); ?>

    <?= DetailView::widget([
        'model' => $amountArr,
        'attributes' => [
            [
                'label' => '总金额',
                'value' => $amountArr['allAmount'],
                'captionOptions' => ['width' => '120px;']
            ],
            [
                'label' => '同城金额',
                'value' => $amountArr['sameCityAmount']
            ],
            [
                'label' => '外阜金额',
                'value' => $amountArr['wAmount']
            ],
        ],
    ]) ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'order_id',
                'value' => function ($model) {
                    return $model->getOrderSn();
                }
            ],
            [
                'attribute' => 'content',
                'value' => function($model) {
                    return $model-> getContent($model->content);
                },
            ],
            'amount',
            [
                'label' => '收款人',
                'attribute' => 'userTrueName.user_truename',
            ],
            'add_time'
        ],
    ]); ?>
</div>
