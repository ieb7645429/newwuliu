<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\assets\SiteSignupAsset;
SiteSignupAsset::register($this);

$this->title = '注册';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'username')->textInput() ?>
                <?= $form->field($model, 'password')->passwordInput() ?>
                <?= $form->field($model, 'email') ?>
                <?= $form->field($model, 'member_phone') ?>
                <?= $form->field($model, 'user_truename') ?>
                <?php 
                echo $form->field($model, 'district')->widget(\chenkby\region\Region::className(),[
                        'model'=>$model,
                        'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                        'province'=>[
                                'attribute'=>'member_provinceid',
                                'items'=>$area::getRegion(),
                                'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份']
                        ],
                        'city'=>[
                                'attribute'=>'member_cityid',
                                'items'=>$area::getRegion($model['member_provinceid']),
                                'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市']
                        ],
                        'district'=>[
                                'attribute'=>'member_areaid',
                                'items'=>$area::getRegion($model['member_cityid']),
                                'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区']
                        ]
                ]);
                ?>
                <?= $form->field($model, 'member_areainfo')->textarea(['rows'=>3])?>
                <div class="form-group">
                    <?= Html::submitButton('提交', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
