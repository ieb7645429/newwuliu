<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\TellerLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teller-log-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>

    <div style="width:350px;">
    <?= $form->field($model, 'add_time')->label('查询时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$model,
            'options' => [
                'class' => 'form-control',
                'value' => Yii::$app->request->get('TellerLogSearch')['add_time'] ? Yii::$app->request->get('TellerLogSearch')['add_time'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
    ])?>
    
    <?= $form->field($model, 'user_id')
             ->label('收款人')
             ->dropDownList($userList);
    ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
