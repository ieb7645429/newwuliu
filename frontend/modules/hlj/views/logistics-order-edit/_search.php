<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderEditSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logistics-order-edit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'order_id') ?>

    <?= $form->field($model, 'logistics_sn') ?>

    <?= $form->field($model, 'goods_sn') ?>

    <?= $form->field($model, 'order_sn') ?>

    <?php // echo $form->field($model, 'freight') ?>

    <?php // echo $form->field($model, 'goods_price') ?>

    <?php // echo $form->field($model, 'make_from_price') ?>

    <?php // echo $form->field($model, 'goods_num') ?>

    <?php // echo $form->field($model, 'order_state') ?>

    <?php // echo $form->field($model, 'state') ?>

    <?php // echo $form->field($model, 'freight_state') ?>

    <?php // echo $form->field($model, 'goods_price_state') ?>

    <?php // echo $form->field($model, 'abnormal') ?>

    <?php // echo $form->field($model, 'collection') ?>

    <?php // echo $form->field($model, 'collection_poundage_one') ?>

    <?php // echo $form->field($model, 'collection_poundage_two') ?>

    <?php // echo $form->field($model, 'order_type') ?>

    <?php // echo $form->field($model, 'add_time') ?>

    <?php // echo $form->field($model, 'member_name') ?>

    <?php // echo $form->field($model, 'member_id') ?>

    <?php // echo $form->field($model, 'member_cityid') ?>

    <?php // echo $form->field($model, 'member_phone') ?>

    <?php // echo $form->field($model, 'receiving_name') ?>

    <?php // echo $form->field($model, 'receiving_phone') ?>

    <?php // echo $form->field($model, 'receiving_name_area') ?>

    <?php // echo $form->field($model, 'receiving_provinceid') ?>

    <?php // echo $form->field($model, 'receiving_cityid') ?>

    <?php // echo $form->field($model, 'receiving_areaid') ?>

    <?php // echo $form->field($model, 'terminus_id') ?>

    <?php // echo $form->field($model, 'logistics_route_id') ?>

    <?php // echo $form->field($model, 'shipping_type') ?>

    <?php // echo $form->field($model, 'employee_id') ?>

    <?php // echo $form->field($model, 'driver_member_id') ?>

    <?php // echo $form->field($model, 'test') ?>

    <?php // echo $form->field($model, 'shipping_sale') ?>

    <?php // echo $form->field($model, 'scale') ?>

    <?php // echo $form->field($model, 'same_city') ?>

    <?php // echo $form->field($model, 'return_logistics_sn') ?>

    <?php // echo $form->field($model, 'edit_time') ?>

    <?php // echo $form->field($model, 'order_remark') ?>

    <?php // echo $form->field($model, 'edit_member_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
