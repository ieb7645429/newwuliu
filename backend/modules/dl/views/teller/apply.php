<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use backend\modules\dl\assets\TellerApplyAsset;

TellerApplyAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel common\models\ApplyForWithdrawalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现记录';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="apply-for-withdrawal-index">
    
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="apply-for-withdrawal-search">
    
        <?php $form = ActiveForm::begin([
            'action' => [Yii::$app->controller->action->id],
            'method' => 'get',
        ]); ?>
    
        <?= $form->field($searchModel, 'user_id')->label('用户电话/用户店铺名') ?>
    
        <?php  echo $form->field($searchModel, 'status')->dropDownList(['' => '全部', '1' => '未付款', '2' => '已付款']) ?>
    
        <div class="form-group">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label' => '申请人',
                'attribute' => 'userTrueName.user_truename',
            ],
            [
                'label' => '开户行',
                'attribute' => 'bankInfo.bank_info_bank_address',
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="status_name_' . $model->id . '">' . $model->getStatusName() . '</span>';
                },
            ],
            [
                'attribute' => 'add_time',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            [
                'label' => '会员号',
                'attribute' => 'userTrueName.username',
            ],
            [
                'label' => '开户名',
                'attribute' => 'bankInfo.bank_info_account_name',
            ],
            [
                'label' => '银行卡号',
                'attribute' => 'bankInfo.bank_info_card_no',
            ],
            
            [
                'label' => '银行',
                'attribute' => 'bankInfo.bank_info_bank_name',
            ],
            'amount',
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{confirm} {confirm2}',
                'buttons' => [
                    'confirm' => function ($url, $model, $key) {
                        return $model->status == '1' ? Html::button('确认付款', ['id'=>'confirm-collection_'.$key,'class' => 'btn btn-danger confirm-collection','data-id'=>$key]): '';
                    },
                    
                    'confirm2' => function ($url, $model, $key) {
                        return $model->status == '1' ? Html::button('线下付款确认', ['id'=>'confirm2-collection_'.$key,'class' => 'btn btn-danger confirm-collection','data-id'=>$key]): '';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
