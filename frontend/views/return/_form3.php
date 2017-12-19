<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Area;
use common\models\ShippingTpye;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="logistics-return-order-form">

    <?php $form = ActiveForm::begin([			
// 			'options'=>array('onsubmit'=>'return Check_GoodsInfo();'),
		]); ?>

    <?php // echo $form->field($model, 'logistics_sn')->textInput(['maxlength' => true]) ?>
<div class="title_bg">
    <span>发货信息</span>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'member_phone')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'member_name')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'member_cityid')->dropDownList(Area::getRegion(6), ['disabled'=>true]) ?>
		<?= Html::input('hidden', 'LogisticsReturnOrder[member_cityid]', $model->member_cityid, []) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'goods_num')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'goods_price')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
</div>

<div class="title_bg">
    <span>收货信息</span>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'receiving_phone')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'receiving_name')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
</div>
<div class="table01">
	<?php
        echo $form->field($model, 'receiving_areaid')->label('收货人地址')->widget(\chenkby\region\Region::className(),[
                'model'=>$model,
                'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                'province'=>[
                        'attribute'=>'receiving_provinceid',
                        'items'=>Area::getRegion(),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份', 'value' => Yii::$app->params['provinceId'], 'disabled' => 'disabled']
                ],
                'city'=>[
                        'attribute'=>'receiving_cityid',
                    'items'=>Area::getRegion(6),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市','disabled' => 'disabled']
                ],
                'district'=>[
                        'attribute'=>'receiving_areaid',
                        'items'=>Area::getRegion($model['receiving_cityid']),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区','disabled' => 'disabled','style'=>'display:none !important']
                ]
        ]);
    ?>
<?= Html::input('hidden', 'LogisticsReturnOrder[receiving_cityid]', $model->receiving_cityid, []) ?>
</div>
<div class="table01">
    <div class="area_div">
        <?= $form->field($model, 'receiving_name_area')->textInput(['maxlength' => true,'readonly'=>true]) ?>
    </div>
</div>
<div class="table01">
	<div class="area_div">
		<?= $form->field($model, 'ship_logistics_sn')->textInput(['maxlength' => true,'readonly' => 'readonly']) ?>
	</div>
</div>
<div class="title_bg">
    <span>运费信息</span>
</div>
<div class="table01">
	<div class="table_big_div">
		<?= $form->field($model, 'shipping_type')->dropDownList(ShippingTpye::getReturnShippingType(), ['disabled'=>true]) ?>
		<?= Html::input('hidden', 'LogisticsReturnOrder[shipping_type]', $model->shipping_type, []) ?>
	</div>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'make_from_price')->textInput(['maxlength' => true,'value'=>empty($model->make_from_price)?0:$model->make_from_price,'readonly'=>true]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'freight')->textInput(['maxlength' => true,'readonly'=>true]) ?>
	</div>
</div>
<div class="form-group">
    <?= $form->field($model, 'order_type')->hiddenInput()->label(false)?>
    <a data-confirm="是否确定保存？"><?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?></a>
</div>

    <?php ActiveForm::end(); ?>

</div>
