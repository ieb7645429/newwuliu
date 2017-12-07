<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\ShippingTpye;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logistics-order-form">

    <?php $form = ActiveForm::begin([			
			'options'=>array('onsubmit'=>'return Check_GoodsInfo();'),
		]); ?>

    <?php // echo $form->field($model, 'logistics_sn')->textInput(['maxlength' => true]); ?>

    <?php // echo $form->field($model, 'order_sn')->textInput(['maxlength' => true]); ?>

    <?php // echo $form->field($model, 'freight')->textInput(['maxlength' => true]); ?>
<div class="title_bg">
    <span>发货人信息</span>
</div>
<div class="table01">
	<div class="table_div" style="display:none">
	<?= $form->field($user, 'username')->textInput(['maxlength' => true, 'value' => Yii::$app->user->getIdentity()->getAttribute('username')]) ?>
	</div>
	<div class="table_div">
	<?= $form->field($model, 'member_phone')->textInput(['maxlength' => true, 'value' => Yii::$app->user->getIdentity()->getAttribute('member_phone')]) ?>
	</div>
	<div class="table_div">
	<?= $form->field($model, 'member_name')->textInput(['maxlength' => true, 'value' => Yii::$app->user->getIdentity()->getAttribute('user_truename') ]) ?>
	</div>
</div>
<div class="table01">
    <div class="table_div">
    <?= $form->field($model, 'member_cityid')->dropDownList($area::getRegion(6), ['value' => Yii::$app->user->getIdentity()->getAttribute('member_cityid')]) ?>
    </div>
    <div class="table_div">
    <?= $form->field($model, 'goods_price')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="title_bg">
    <span>收货人信息</span>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'receiving_phone')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'receiving_name')->textInput(['maxlength' => true]) ?>
	</div>
</div>
<div class="table01">
	<?php 
        echo $form->field($model, 'receiving_areaid')->label('收货人地址')->widget(\chenkby\region\Region::className(),[
                'model'=>$model,
                'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                'province'=>[
                        'attribute'=>'receiving_provinceid',
                        'items'=>$area::getRegion(),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份', 'value' => 6, 'disabled' => 'disabled']
                ],
                'city'=>[
                        'attribute'=>'receiving_cityid',
                        'items'=>$area::getRegion(6),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市']
                ],
                'district'=>[
                        'attribute'=>'receiving_areaid',
                        'items'=>$area::getRegion($model['receiving_cityid']),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区']
                ]
        ]);
    ?>
</div>
<div class="table01">
	<div class="area_div">
		<?= $form->field($model, 'receiving_name_area')->textInput(['maxlength' => true]) ?>
	</div>
</div>
<div class="title_bg">
    <span>运费信息</span>
</div>
<div class="table01">
<?= $form->field($model, 'collection')->dropDownList($model::getCollectionList()) ?>
</div>
<div class="table01">
<?= $form->field($model, 'shipping_type')->dropDownList(ShippingTpye::getShippingType()) ?>
</div>
    

    

    <?php // echo $form->field($model, 'make_from_price')->textInput(['maxlength' => true]); ?>

    <?php // echo $form->field($model, 'goods_num')->textInput(); ?>

    <?php // echo $form->field($model, 'order_state')->textInput(); ?>

    <?php // echo $form->field($model, 'state')->textInput(); ?>

    <?php // echo $form->field($model, 'abnormal')->textInput(); ?>

    

    <?php // echo $form->field($model, 'collection_poundage_one')->textInput(['maxlength' => true]); ?>

    <?php // echo $form->field($model, 'collection_poundage_two')->textInput(['maxlength' => true]); ?>

    <?php // echo $form->field($model, 'order_type')->textInput(); ?>

    <?php // echo $form->field($model, 'add_time')->textInput(['maxlength' => true]); ?>
    <?php // echo $form->field($model, 'member_id')->hiddenInput(); ?>

    
    

    

    

    

    <?php // echo $form->field($model, 'receiving_cityid')->textInput(); ?>

    <?php // echo $form->field($model, 'receiving_areaid')->textInput(); ?>

    <?php // echo $form->field($model, 'terminus_id')->textInput() ?>

    <?php // echo $form->field($model, 'logistics_route_id')->textInput() ?>

    <?php // echo $form->field($model, 'logistics_car_id')->textInput() ?>

    <?php // echo $form->field($model, 'driver_member_id')->textInput() ?>
 <div class="form-group field-info-goods_info">
 <div class="title_bg">
    <span>商品详细信息</span>
</div>
        <div class="table01">
           <div class="table_div">商品名称:<input type="text" class="form-control" name="GoodsInfo[name][]"></div>
           <div class="table_div">商品数量:<input type="text" class="form-control" name="GoodsInfo[number][]" value=""></div>
           <div class="table_div">商品价钱:<input type="text" class="form-control" name="GoodsInfo[price][]" value=""></div>
        </div>
              <a id="logisticsorder-add_goods_price" href="" class="btn btn-success add-button" onclick="return false;">添加商品</a>
              <div class="help-block"></div>
     </div>
    <div class="form-group">
        <a data-confirm="是否确定保存？"><?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?></a>
    </div>

    <?php ActiveForm::end(); ?>

</div>
