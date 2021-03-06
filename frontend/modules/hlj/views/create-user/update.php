<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '会员: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
$this->params['leftmenus'] = $menus;
?>
<div class="user-update">

    <h2><?= Html::encode($this->title) ?></h2>

	<hr style="width:50%;margin:20px 0px;"></hr>

	<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['style'=>'width:50%']) ?>
    <?= $form->field($model, 'user_truename')->textInput(['style'=>'width:50%'])->label('真实姓名') ?>
    <?= $form->field($model, 'member_phone')->textInput(['style'=>'width:50%'])->label('电话') ?>
    <?php if(!empty($bank)):?>
    	<?= $form->field($bank, 'bank_info_card_no',['labelOptions' => ['label' => '银行卡号']])->textInput(['maxlength' => true,'style'=>'width:50%','value' => $bank->bank_info_card_no]) ?>
    	<?= $form->field($bank, 'bank_info_account_name',['labelOptions' => ['label' => '开户名']])->textInput(['maxlength' => true,'style'=>'width:50%','value' => $bank->bank_info_account_name]) ?>
    	<?= $form->field($bank, 'bank_info_bank_name',['labelOptions' => ['label' => '开户行名称']])->textInput(['maxlength' => true,'style'=>'width:50%','value' => $bank->bank_info_bank_name]) ?>
    	<?= $form->field($bank, 'bank_info_bank_address',['labelOptions' => ['label' => '开户行地址']])->textInput(['maxlength' => true,'style'=>'width:50%','value' => $bank->bank_info_bank_address]) ?>
    <?php endif;?>
    <div>
    	<a data-confirm="是否确定修改？"><?= Html::Button('保存', ['class' => 'btn btn-success']) ?></a>
    </div>
</div>

    <?php ActiveForm::end(); ?>

</div>

</div>