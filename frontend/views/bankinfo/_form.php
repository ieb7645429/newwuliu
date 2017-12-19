<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Bankinfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bankinfo-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->hiddenInput(['value'=>Yii::$app->user->id])->label(false) ?>
    <?= $form->field($model, 'bank_info_card_no')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'bank_info_account_name')->textInput(['maxlength' => true]) ?>
    <?php // $form->field($model, 'bank_info_bank_name')->dropdownList($bankname,array('class'=>'form-control'));?>
    <?= $form->field($model, 'bank_info_bank_name')->dropDownList(Yii::$app->params['bankname'],['options'=>[$model->bank_info_bank_name=>['Selected'=>true]]]) ?>
    <?= $form->field($model, 'bank_info_bank_address')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'bank_info_place')->hiddenInput(['value'=>$source])->label(false) ?>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
