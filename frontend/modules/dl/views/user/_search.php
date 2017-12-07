<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?= $form->field($model, 'password_hash') ?>

    <?= $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'user_truename') ?>

    <?php // echo $form->field($model, 'is_poundage') ?>

    <?php // echo $form->field($model, 'is_buy_out') ?>

    <?php // echo $form->field($model, 'buy_out_price') ?>

    <?php // echo $form->field($model, 'buy_out_time') ?>

    <?php // echo $form->field($model, 'member_phone') ?>

    <?php // echo $form->field($model, 'member_areaid') ?>

    <?php // echo $form->field($model, 'member_cityid') ?>

    <?php // echo $form->field($model, 'member_provinceid') ?>

    <?php // echo $form->field($model, 'member_areainfo') ?>

    <?php // echo $form->field($model, 'App_Key') ?>

    <?php // echo $form->field($model, 'small_name') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
