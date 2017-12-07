<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\BalanceLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="balance-log-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'uid')->label('用户电话/用户店铺名/用户小号') ?>

    <?php  echo $form->field($model, 'order_sn') ?>


    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
