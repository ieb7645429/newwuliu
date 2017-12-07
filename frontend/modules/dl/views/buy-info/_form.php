<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\buyinfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="buyinfo-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>



    <?= $form->field($model, 'area_info')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'is_receive')->dropDownList(['1'=>'是','0'=>'否'])?>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
