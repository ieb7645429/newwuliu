<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\models\CustomerSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="customer-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php //$form->field($model, 'id') ?>

    <?php //$form->field($model, 'name') ?>

    <?php //$form->field($model, 'customer_type') ?>

    <?php //$form->field($model, 'customer_num') ?>

    <?= $form->field($model, 'customer_name')->label('客户名称') ?>

    <?php echo $form->field($model, 'contact_person')->label('联系人') ?>

    <?php  echo $form->field($model, 'telephone')->label('座机') ?>

    <?php  echo $form->field($model, 'mobilephone')->label('手机') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'coord') ?>

    <?php // echo $form->field($model, 'create_time') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php  //echo $form->field($model, 'route')->label('线路') ?>

    <?php // echo $form->field($model, 'maccount_having') ?>

    <?php // echo $form->field($model, 'mall_account') ?>

    <?php // echo $form->field($model, 'open_up') ?>

    <?php // echo $form->field($model, 'collection') ?>

    <?php // echo $form->field($model, 'ultimate') ?>

    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?php //echo Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
        <?= Html::a('重置', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
