<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RouteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logistics-route-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

<!--    --><?php //echo $form->field($model, 'logistics_route_id') ?>

<!--    --><?php //echo $form->field($model, 'logistics_route_code') ?>

<!--    --><?php //echo $form->field($model, 'logistics_route_no') ?>

<!--    --><?php //echo $form->field($model, 'logistics_route_name') ?>

<!--    --><?php //echo $form->field($model, 'same_city') ?>

    <?php echo $form->field($model, 'username')->label('按司机搜索:') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
<!--        --><?php //echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
        <?php echo Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>