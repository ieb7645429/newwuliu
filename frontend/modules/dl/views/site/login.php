<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\assets\SiteLoginAsset;

$this->title = '登录';
$this->params['breadcrumbs'][] = $this->title;
SiteLoginAsset::register($this);
?>
<div class="bg">
<div class="form">
 <?php
	  $form = ActiveForm::begin([
			'id' => 'login-form',
		])
  ?>
<div class="wid">
  <?= $form->field($model, 'username')->textInput(array('class'=>'_input','placeholder'=>'请输入用户名','autofocus' => true))->label(false) ?>
</div>
<div class="wid  mt-11" >
<?= $form->field($model, 'password')->passwordInput()->textInput(array('type'=>'password','class'=>'_input','placeholder'=>'请输入密码'))->label(false) ?>
</div>
<div class="wid  mt-25">
 <?= Html::submitButton('', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?></div>
 <?php ActiveForm::end() ?>
</div>
<div class="foot">中联大川供应链管理有限公司</div>
</div>
