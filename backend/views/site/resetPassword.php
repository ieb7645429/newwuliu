<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use backend\assets\ResetPasswordAsset;
ResetPasswordAsset::register($this);
$this->title = '设置新密码';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-reset-password">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'reset-password-form','options'=>array('onsubmit'=>'return ok();')]); ?>
                <div>
                 <label for="oldpwd" id="oldpwd-label">旧密码</label>
                 <input type="password" name="oldpwd" id="oldpwd"  class="form-control"/>
                 <p id="oldpwd-p" style="color:#a94442; height:20px"></p>
                </div>
                <div>
                <?= $form->field($model, 'password')->passwordInput(['autofocus' => true])->label('新密码') ?>
                </div>
                <div>
                 <label for="newpwd" id="newpwd-label">重复密码</label>
                 <input type="password" name="newpwd" id="newpwd" class="form-control"/>
                 <p id="newpwd-p" style="color:#a94442; height:20px"></p>
                </div>
 
                <div class="form-group" style="margin-top:10px">
                    <?= Html::submitButton('保存', ['class' => 'btn btn-primary','disabled'=>true,'id'=>'changepwd']) ?>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
