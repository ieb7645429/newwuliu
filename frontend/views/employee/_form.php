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
        
        <?= $form->field($model, 'order_type')->dropDownList(array('1'=>'西部','3'=>'瑞胜', '4'=>'塔湾'),['options'=>[$model->order_type=>['Selected'=>true]],'disabled'=>$disabled]) ?>
       <?php if($disabled){?>
       	<input type="hidden" name="LogisticsOrder[order_type]" value="<?= $model->order_type?>">
       	<?php }?>
        </div>      
        
</div>
       <!-- <div id="order_sn">
        <?php //$form->field($model, 'order_sn')->textInput(['maxlength' => true,'readonly'=>$disabled]) ?>
        </div>-->
     <?php
       if($model->order_sn):
	    $ordersn = explode(',',unserialize($model->order_sn));
	    foreach($ordersn as $key => $val):
	 ?>
     <div class="table01">
     <div class="table_div">订单编号:<input value="<?=$val?>" type="text"   list="sn_list" id="logisticsorder-order_sn" class="form-control sn" name="OrderSn[]" onkeyup="javascript:SearchSn(this.value);" disabled="disabled"></div>
     <datalist id="sn_list" >
     </datalist>
     </div>
     <?php
	  endforeach;
	  else:
	  ?>
   <div class="table01">
    <div class="table_div">订单编号:<input  type="text"   list="sn_list" id="logisticsorder-order_sn" class="form-control" onblur="javascript:GetValue(this.value);" name="OrderSn[]" onkeyup="javascript:SearchSn(this.value);"></div>
     <datalist id="sn_list" >
     </datalist>
     </div>
    <a id="logisticsorder-add_order_sn" href="" class="btn btn-success add-button" onclick="return false;">添加订单编号</a>

      <?php
      endif;
	 ?>
  <div class="help-block"></div>
<div class="title_bg">
    <span>发货信息</span>
    <input type='hidden' name='hidname' id='hidname'>
</div>
<div class="table01">
    <div class="table_div">
        <?= $form->field($user, 'username')->textInput(['maxlength' => true]) ?>
    </div>

    
    <div class="table_div" style="position:relative">
        <?= $form->field($model, 'member_name')->textInput(['maxlength' => true,'list'=>'member_name_list','onkeyup'=>"javascript:SearchMemberName(this.value);"]) ?>
         <datalist id="member_name_list" >
         
          </datalist>
    </div>
    <div class="table_div">
        <?= $form->field($model, 'member_phone')->textInput(['maxlength' => true]) ?>
    </div>
</div>
    <div class="table01">
        <div class="table_div">
        <?= $form->field($user, 'small_num')->textInput(['maxlength' => true]) ?>
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
    <!--返货率-->
    <span id="logisticsorder-return_goods_rate"></span>
</div>
<div class="table02">
    <div class="displayBlock">
    <?= $form->field($model, 'receiving_provinceid')->dropDownList($area::getRegion(), ['value' => 6, 'disabled' => true]) ?>
    </div>
    <div id="search-form" data-search-value="<?= $areaName ?>" class="displayBlock"></div>
    <?= Html::input('hidden', 'LogisticsOrder[receiving_cityid]', $model->receiving_cityid ? $model->receiving_cityid : 107, ['id'=>'logisticsorder-receiving_cityid']) ?>
    <?= Html::input('hidden', 'LogisticsOrder[receiving_areaid]', $model->receiving_areaid ? $model->receiving_areaid : 0, ['id'=>'logisticsorder-receiving_areaid']) ?>
</div>
<div class="table01">
    <div class="area_div">
    <?= $form->field($model, 'receiving_name_area')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="table01">
    <div class="table_div">
        <?= $form->field($model, 'logistics_route_id')->dropDownList($logisticsRouteInfo,['prompt' => '请选择路线']);?>
    </div>
    <div class="table_div terminusSelect <?php if($this->context->action->id=='create'||$model->same_city==1):?>terminus_list<?php endif;?>">
        <?= $form->field($model, 'terminus_id')->dropDownList($terminus, $this->context->action->id=='create'||$model->same_city==1?['disabled' =>'true']:[]);?>
    </div>
</div>
<div class="title_bg">
    <span>运费信息</span>
</div>
<div class="table01">
	<div class="table_div">
	<?php 
	$role = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))[0];
    ?>
    <?= $form->field($model, 'collection')->dropDownList($model::getCollectionList(),[]) ?>
    </div>
    <div class="table_div no-charge">
    <?php 
    if(!empty($model->order_sn)){
        $readonly = true;
    }else{
        $readonly = false;
    }
    ?>
    <?= $form->field($model, 'goods_price')->textInput(['maxlength' => true,'value'=>$model->goods_price,'readonly'=>$readonly]) ?>
    <?= Html::input('hidden','save_price','',['id'=>'save_price'])?>
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
    <?= $form->field($model, 'make_from_price')->textInput(['maxlength' => true, 'value' => 0,'readonly'=>'readonly']) ?>
    <?php //= $form->field($model, 'make_from_price')->textInput(['maxlength' => true, 'value' => empty($model->make_from_price)?0:$model->make_from_price]) ?>
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

<div class="title_bg">
<span>其他信息</span>
</div>
<div class="table01">
<?php 
//修改页面  显示内容
    if(Yii::$app->controller->action->id=='update'&&$role==Yii::$app->params['roleEmployeeDelete']):
?>
	<div class="table_div">
    	<?= $form->field($model, 'abnormal')->dropDownList(array('1'=>'异常','2'=>'正常'),['style'=>'width:50%','options'=>[$model->abnormal=>['Selected'=>true]]])->label("是否异常");?>
    </div>
<?php endif;?>
    <div class="table_area">
    	<?= $form->field($orderRemark, 'edit_content',['labelOptions' => ['label' => '备注']])->textarea(['rows'=>5,'style'=>'width:50%'])?>
	</div>
</div>

    <?php // echo $form->field($model, 'logistics_route_id')->textInput() ?>
    <div class="form-group">
        <input type="hidden" id="status"  value="0">
        <input type="hidden" id="ismodify"  value="0">
        <?= Html::Button('保存', ['class' => 'btn btn-success','id'=>'submitButton']) ?>
    </div>


    <div class="title_bg">
        <span>配件信息</span>
    </div>
    <div class="table01">
        <div class="checkbox form-inline">
            <?php foreach ($parts::getParts() as $key=>$val){?>
            <?= $form->field($orderParts, 'p'.$key)->checkbox(['value'=>'1','label'=>$val]) ?>
            <?php } ?>

        </div>
    </div>
    <div class="form-group">
        <?= Html::Button('保存', ['class' => 'btn btn-success','id'=>'submitButton2']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

