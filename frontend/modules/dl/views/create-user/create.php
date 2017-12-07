<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\modules\dl\assets\CreateUserCreateAsset;
CreateUserCreateAsset::register($this);

$this->title = '注册';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
                <?= $form->field($model, 'username')->label('')->hiddenInput() ?>
                <?= $form->field($model, 'member_phone') ?>
                <?= $form->field($model, 'password')->label('')->hiddenInput(['value' => '123456']) ?>
                <?= $form->field($model, 'user_truename')->label('店铺名称')?>
                <?php 
                echo $form->field($model, 'district')->widget(\chenkby\region\Region::className(),[
                        'model'=>$model,
                        'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                        'province'=>[
                            'attribute'=>'member_provinceid',
                            'items'=>$area::getRegion(),
                            'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份', 'value' => 6, 'disabled' => 'disabled']
                        ],
                        'city'=>[
                            'attribute'=>'member_cityid',
                            'items'=>$area::getRegion(6),
                            'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市', 'value' => 108, 'disabled' => 'disabled']
                        ],
                        'district'=>[
                            'attribute'=>'member_areaid',
                            'items'=>$area::getRegion(108),
                            'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区', ]
                        ]
                ]);
                ?>
                <?= Html::input('hidden', 'CreateUserForm[member_provinceid]', 6, []) ?>
                <?= Html::input('hidden', 'CreateUserForm[member_cityid]', 108, []) ?>
                <?= $form->field($model, 'member_areainfo')->textarea(['rows'=>3])?>
                <?= $form->field($model, 'small_num')->label('')->hiddenInput() ?>
                <div class="form-group">
                    <?= Html::button('提交', ['class' => 'btn btn-primary', 'name' => 'signup-button', 'id' => 'create-user-create']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
