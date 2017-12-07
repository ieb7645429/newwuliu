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
			'options'=>array('onsubmit'=>'return Check_GoodsInfo();'),
		]); ?>

    <?php // echo $form->field($model, 'logistics_sn')->textInput(['maxlength' => true]) ?>
<div class="title_bg">
    <span>发货信息</span>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'member_phone')->textInput(['maxlength' => true]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'member_name')->textInput(['maxlength' => true]) ?>
	</div>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'member_cityid')->dropDownList(Area::getRegion(6), []) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'goods_num')->textInput() ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'goods_price')->textInput(['maxlength' => true]) ?>
	</div>
</div>

<div class="title_bg">
    <span>收货信息</span>
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
                        'items'=>Area::getRegion(),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份', 'value' => Yii::$app->params['provinceId'], 'disabled' => 'disabled']
                ],
                'city'=>[
                        'attribute'=>'receiving_cityid',
                    'items'=>Area::getRegion(6),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市']
                ],
                'district'=>[
                        'attribute'=>'receiving_areaid',
                        'items'=>Area::getRegion($model['receiving_cityid']),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区','disabled' => 'disabled','style'=>'display:none !important']
                ]
        ]);
    ?>
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
		<?= $form->field($model, 'shipping_type')->dropDownList(ShippingTpye::getReturnShippingType()) ?>
	</div>
</div>
<div class="table01">
	<div class="table_div">
		<?= $form->field($model, 'make_from_price')->textInput(['maxlength' => true,'value'=>empty($model->make_from_price)?0:$model->make_from_price]) ?>
	</div>
	<div class="table_div">
		<?= $form->field($model, 'freight')->textInput(['maxlength' => true]) ?>
	</div>
</div>

    

    <?php // echo $form->field($model, 'goods_sn')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>

    

    
<div id="goodInfo">
    <?php if(!$model->isNewRecord && !empty($returnInfo)){?>
            <div class="form-group field-returninfo-goods_info">
                <div class="title_bg">
                    <span>退货商品信息</span>
                </div>
              <?php foreach ($returnInfo as $v):?>
              <div class="table01">
               <div class="table_div">商品名称:<input type="text" class="form-control" name="ReturnInfo[name][]" value="<?=$v['name']?>"></div>
               <div class="table_div">商品数量:<input type="text" class="form-control" name="ReturnInfo[number][]" value="<?=$v['number']?>"></div>
               <div class="table_div">商品价钱:<input type="text" class="form-control" name="ReturnInfo[price][]" value="<?=$v['price']?>"></div>
            </div>
              <?php endforeach;?>
              <a id="logisticsreturnorder-add_goods_price" href="" class="btn btn-success add-button" onclick="return false;">添加商品</a>
              <div class="help-block"></div>
             </div>
    <?php }else{?>
           <div class="form-group field-returninfo-goods_info">
                <div class="title_bg">
                    <span>退货商品信息</span>
                </div>
              <div class="table01">
               <div class="table_div">商品名称:<input type="text" class="form-control" name="ReturnInfo[name][]" ></div>
               <div class="table_div">商品数量:<input type="text" class="form-control" name="ReturnInfo[number][]"></div>
               <div class="table_div">商品价钱:<input type="text" class="form-control" name="ReturnInfo[price][]"></div>
            </div>
              <a id="logisticsreturnorder-add_goods_price"  class="btn btn-success add-button">添加商品</a>
              <div class="help-block"></div>
             </div>
     <?php }?>
</div>
    

    

    

    <?php // echo $form->field($model, 'order_state')->textInput() ?>

    <?php // echo $form->field($model, 'state')->textInput() ?>

    <?php // echo $form->field($model, 'abnormal')->textInput() ?>

    <?php // echo $form->field($model, 'collection')->dropDownList($model::getCollectionList()) ?>

    <?php // echo $form->field($model, 'collection_poundage_one')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'collection_poundage_two')->textInput(['maxlength' => true]) ?>

    <?php // echo $form->field($model, 'order_type')->textInput() ?>

    <?php // echo $form->field($model, 'return_type')->textInput() ?>

    <?php // echo $form->field($model, 'add_time')->textInput(['maxlength' => true]) ?>
    

    <?php // echo $form->field($model, 'member_id')->textInput() ?>

    

    

    

    

    

    <?php // echo $form->field($model, 'terminus_id')->textInput() ?>

    <?php // echo $form->field($model, 'logistics_route_id')->textInput() ?>
    
    <div class="table01">
        <div class="table_area">
        <?= $form->field($orderRemark, 'edit_content',['labelOptions' => ['label' => '备注']])->textarea(['rows'=>5,'style'=>'width:50%'])?>
        </div>
    </div>

    <div class="form-group">
        <a data-confirm="是否确定保存？"><?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?></a>
    </div>

    <?php ActiveForm::end(); ?>

</div>
