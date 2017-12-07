<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
use frontend\modules\dl\assets\BalanceLogBalanceAsset;
BalanceLogBalanceAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserBalanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户余额';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">申请提现</h4>',
]);?>
<div class="payDiv">
<div class="payInput">
<?=Html::input('text','money','',['id'=>'money','class' => 'form-control pay-input','placeholder'=>'输入提现金额']);?>
    </div>
    <div class="payPromptDiv"><div class="payPrompt">输入金额必须为整数</div></div>
    <div class="row payButtonDiv">
    <div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary','disabled'=>'true']);?></div>
    <div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
</div>
</div>
<?php Modal::end();?>

<div class="user-balance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="user-balance-search">

        <?php $form = ActiveForm::begin([
            'action' => [Yii::$app->controller->action->id],
            'method' => 'get',
        ]); ?>
    
        <?= $form->field($searchModel, 'user_id') ?>

        <div class="form-group">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'user.user_truename',
                'label' => '用户'
            ],
            'user_amount',
            'withdrawal_amount',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{confirm}',
                'buttons' => [
                    'confirm' => function ($url, $model, $key) {
                    return Html::a('申请提现', '#', [
                                'id' => 'create'.$model->user_id,
                                'data-toggle' => 'modal',
                                'data-target' => '#create-modal',
                                'data-user-id' => $model->user_id,
                                'data-amount' => $model->withdrawal_amount,
                                'class' => 'btn btn-success confirm',
                          ]);
                    },
                ]
            ],
        ],
    ]); ?>
    
    <input type="hidden" id="amount" value="">
    <input type="hidden" id="userId" value="">
</div>
