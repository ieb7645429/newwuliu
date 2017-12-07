<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Bankinfosearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bankinfo-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'bank_info_id') ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'bank_info_card_no') ?>

    <?= $form->field($model, 'bank_info_account_name') ?>

    <?= $form->field($model, 'bank_info_bank_name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
