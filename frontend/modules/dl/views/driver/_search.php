<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
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

    <div style="width:350px;">
    <?= $form->field($model, 'add_time')->label('查询时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$model,
            'options' => [
                'class' => 'form-control',
                'value' => Yii::$app->request->get('LogisticsOrderSearch')['add_time'] ? Yii::$app->request->get('LogisticsOrderSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
    ])?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?php // echo Html::resetButton('重置', ['class' => 'btn btn-default']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
