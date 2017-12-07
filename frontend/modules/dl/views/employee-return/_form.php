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
<div class="title_bg">
    <span>订单信息</span>
</div>
<div class="table01">
        <?php // echo $form->field($model, 'order_sn')->textInput(['maxlength' => true]) ?>
        <div class="table_div">
        <!-- '5'=>'有件网' -->
        
        <?= $form->field($model, 'order_type')->dropDownList(array('1'=>'线下','3'=>'瑞胜','5'=>'友件网'),['options'=>[$model->order_type=>['Selected'=>true]],'disabled'=>$disabled]) ?>
       
       	<input type="hidden" name="LogisticsOrder[order_type]" value="<?= $model->order_type?>">
        </div>
        <div id="order_sn" style=" <?php if(empty($model->order_type)||$model->order_type==1){?>display:none<?php }?>">
        <?= $form->field($model, 'order_sn')->textInput(['maxlength' => true,'readonly'=>$disabled]) ?>
        </div>
        
</div>
<div class="title_bg">
    <span>发货信息</span>
</div>
<div class="table01">
    <div class="table_div">
        <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="table_div">
        <?= $form->field($model, 'member_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="table_div">
        <?= $form->field($model, 'member_phone')->textInput(['maxlength' => true]) ?>
    </div>
    
</div>
<div class="table01">
    <div class="table_div">
    <?= $form->field($model, 'member_cityid')->dropDownList($area::getRegion(6), []) ?>
    </div>
    <div class="table_div">
    <?= $form->field($model, 'goods_num')->textInput() ?>
    </div>
</div>
    <?php // echo $form->field($model, 'order_state')->textInput(); ?>

    <?php // echo $form->field($model, 'state')->textInput(); ?>

    <?php // echo $form->field($model, 'abnormal')->textInput(); ?>

   
   
    <?php // echo $form->field($model, 'order_type')->textInput() ?>

    <?php // echo $form->field($model, 'add_time')->textInput(['maxlength' => true]) ?>
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
<div class="table01">
    <div class="table_div">
        <?= $form->field($model, 'logistics_route_id')->dropDownList($logisticsRouteInfo);?>
    </div>
    <div class="table_div <?php if($this->context->action->id=='create'||$model->same_city==1):?>terminus_list<?php endif;?>">
        <?= $form->field($model, 'terminus_id')->dropDownList($terminus, $this->context->action->id=='create'||$model->same_city==1?['disabled' =>'true']:[]);?>
    </div>
</div>
<div class="title_bg">
    <span>运费信息</span>
</div>
<div class="table01">
	<div class="table_div">
    <?= $form->field($model, 'collection')->dropDownList($model::getCollectionList()) ?>
    </div>
    <div class="table_div no-charge">
    <?= $form->field($model, 'goods_price')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="table01">
    <?= $form->field($model, 'shipping_type')->dropDownList(ShippingTpye::getShippingType()) ?>
</div>
<div class="table01">
<!--<div class="table_div_02 no-charge">
    <?= $form->field($model, 'collection_poundage_one')->textInput(['maxlength' => true, 'value' => 1]) ?>
</div>-->
<div class="table_div_02 no-charge">
    <?= $form->field($model, 'collection_poundage_two')->textInput(['maxlength' => true, 'value' => 2]) ?>
</div>
<div class="table_div_02">
    <?= $form->field($model, 'make_from_price')->textInput(['maxlength' => true, 'value' => empty($model->make_from_price)?1:$model->make_from_price]) ?>
</div>
<div class="table_div_02">
    <?= $form->field($model, 'freight')->textInput(['maxlength' => true]) ?>
</div>
       
       
    
</div>
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

    <?php // echo $form->field($model, 'logistics_route_id')->textInput() ?>
    <div class="form-group">
        <a data-confirm="是否确定保存？"><?= Html::Button('保存', ['class' => 'btn btn-success']) ?></a>
    </div>

    <?php ActiveForm::end(); ?>

</div>

