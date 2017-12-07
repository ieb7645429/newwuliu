<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use components\page\GoPager;
use backend\assets\TellerApplyAsset;

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
    <div class="apply-for-withdrawal-search" style="width:300px;">
    
        <?php $form = ActiveForm::begin([
            'action' => [Yii::$app->controller->action->id],
            'method' => 'get',
        ]); ?>
    
        <?= $form->field($searchModel, 'user_id')->label('用户电话/用户店铺名') ?>
    
        <?php  echo $form->field($searchModel, 'status')->dropDownList(['' => '全部', '1' => '未付款', '2' => '已付款']) ?>

<!--        --><?php //echo $form->field($searchModel, 'add_time')->label('申请时间')->widget(DateRangePicker::classname(), [
//                'convertFormat'=>true,
//                'presetDropdown'=>true,
//                'model'=>$searchModel,
//                'options' => [
//                    'class' => 'form-control',
//                    'value' => !empty(Yii::$app->request->get('ApplyForWithdrawalSearch')['add_time']) ? Yii::$app->request->get('TellerIncomeSnLogSearch')['add_time'] : date('Y-m-d') . ' - ' . date('Y-m-d'),
//                ],
//                'pluginOptions'=>[
//                    'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
//                ]
//        ])?>
        
        <?= $form->field($searchModel, 'bank_info_account_name')->label('开户名') ?>
        
        <?= $form->field($searchModel, 'bank_info_card_no')->label('卡号') ?>
    
        <div class="form-group">
            <?= Html::hiddenInput('download_type', '0', ['id' => 'download_type']) ?>
            <?= Html::Button('查询', ['class' => 'btn btn-primary', 'id' => 'searchButton']) ?>
            <?php echo Html::Button('导出Excel', ['class' => 'btn btn-warning', 'id' => 'downloadExcel']); ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' =>[
            'class' => GoPager::className(),
            'firstPageLabel' => '首页',
            'prevPageLabel' => '《',
            'nextPageLabel' => '》',
            'lastPageLabel' => '尾页',
            'goPageLabel' => true,
            'totalPageLable' => '共x页',
            'goButtonLable' => 'GO',
            'maxButtonCount' => 10
        ],
        'columns' => [
            [
                'label' => '付款人',
                'contentOptions' => ['class' => 'pay_user'],
                'attribute' => 'adminUserName.username',
            ],
            [
                'attribute' => 'pay_time',
                'contentOptions' => ['class' => 'pay_time'],
                'label' => '付款时间',
                'format' => ['date', 'php:Y-m-d H:i'],
                
            ],
            [
                'label' => '申请人',
                'attribute' => 'userTrueName.user_truename',
            ],
            [
                'label' => '开户行',
                'attribute' => 'bankInfo.bank_info_bank_address',
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
                'attribute' => 'status',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="status_name_' . $model->id . '">' . $model->getStatusName() . '</span>';
                },
            ],
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
