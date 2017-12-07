<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
?>
<div style="margin-top:20px;">
<?php $form = ActiveForm::begin()?>
<div style="width:350px;">
<?= $form->field($model, 'withdrawal_time')->label('时间查询')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$model,
            'options' => [
                'class' => 'form-control',
                'value' => $withdrawal_time ? $withdrawal_time['date'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
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