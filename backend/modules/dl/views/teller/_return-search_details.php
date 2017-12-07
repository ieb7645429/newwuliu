<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logistics-order-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>
    
    <?php if(Yii::$app->request->get('terminus_id')):?>
    <?= Html::input('hidden', 'terminus_id', Yii::$app->request->get('terminus_id'))?>
    <?php endif;?>
    
    <?php if(Yii::$app->request->get('employee_id')):?>
    <?= Html::input('hidden', 'employee_id', Yii::$app->request->get('employee_id'))?>
    <?php endif;?>
    
    <?php if(Yii::$app->request->get('return_manage_id')):?>
    <?= Html::input('hidden', 'return_manage_id', Yii::$app->request->get('return_manage_id'))?>
    <?php endif;?>

    <div style="width:350px;">
    <?= $form->field($model, 'add_time')->label('查询时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$model,
            'options' => [
                'class' => 'form-control',
                'value' => Yii::$app->request->get('LogisticsReturnOrderSearch')['add_time'] ? Yii::$app->request->get('LogisticsReturnOrderSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
    ])?>
    
    <?= $form->field($model, 'goods_price_state')
             ->label('收款类型')
             ->dropDownList(['0' => '全部', '1'=>'已收', '2'=>'未收'],
                            ['value' => ArrayHelper::getValue(Yii::$app->request->get('LogisticsReturnOrderSearch'), 'goods_price_state', '0')]);
    ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?php // echo Html::resetButton('重置', ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
