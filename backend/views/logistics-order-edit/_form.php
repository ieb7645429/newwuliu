<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderEdit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logistics-order-edit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput() ?>

    <?= $form->field($model, 'logistics_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'freight')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'make_from_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_num')->textInput() ?>

    <?= $form->field($model, 'order_state')->textInput() ?>

    <?= $form->field($model, 'state')->textInput() ?>

    <?= $form->field($model, 'freight_state')->textInput() ?>

    <?= $form->field($model, 'goods_price_state')->textInput() ?>

    <?= $form->field($model, 'abnormal')->textInput() ?>

    <?= $form->field($model, 'collection')->textInput() ?>

    <?= $form->field($model, 'collection_poundage_one')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'collection_poundage_two')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'order_type')->textInput() ?>

    <?= $form->field($model, 'add_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'member_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'member_id')->textInput() ?>

    <?= $form->field($model, 'member_cityid')->textInput() ?>

    <?= $form->field($model, 'member_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiving_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiving_phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiving_name_area')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'receiving_provinceid')->textInput() ?>

    <?= $form->field($model, 'receiving_cityid')->textInput() ?>

    <?= $form->field($model, 'receiving_areaid')->textInput() ?>

    <?= $form->field($model, 'terminus_id')->textInput() ?>

    <?= $form->field($model, 'logistics_route_id')->textInput() ?>

    <?= $form->field($model, 'shipping_type')->textInput() ?>

    <?= $form->field($model, 'employee_id')->textInput() ?>

    <?= $form->field($model, 'driver_member_id')->textInput() ?>

    <?= $form->field($model, 'test')->textInput() ?>

    <?= $form->field($model, 'shipping_sale')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'scale')->textInput() ?>

    <?= $form->field($model, 'same_city')->textInput() ?>

    <?= $form->field($model, 'return_logistics_sn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'edit_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'edit_member_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
