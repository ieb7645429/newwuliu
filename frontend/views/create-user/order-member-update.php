<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = '发货人修改';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'logistics-order']); ?>
                <?= $form->field($model, 'logistics_sn') ?>
                <?= $form->field($model, 'member_phone')->label('发货人电话/小号')?>
                 
                <div class="form-group">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'id' => 'create-user-create', 'data-confirm'=>'确定提交订单？']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
