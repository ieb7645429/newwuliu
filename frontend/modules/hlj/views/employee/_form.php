<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\modules\hlj\models\ShippingTpye;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */
/* @var $form yii\widgets\ActiveForm */
?>
<style>
    .form-control:focus {
        border-width:2px;
        border-color: #0000ff;
    }
</style>
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
        
        <?= $form->field($model, 'order_type')->dropDownList(array('1'=>'通达','3'=>'宣化'),['options'=>[$model->order_type=>['Selected'=>true]],'disabled'=>$disabled]) ?>
       <?php if($disabled){?>
       	<input type="hidden" name="LogisticsOrder[order_type]"  value="<?= $model->order_type?>">
       	<?php }?>
        </div>      
        
</div>
<div class="title_bg">
    <span>发货信息</span>
</div>
<!--relative start-->
<div style="position:relative; z-index:1000">
<div class="table01">
    <div class="table_div">

        <?= $form->field($model, 'member_name')->textInput(['maxlength' => true,'autocomplete'=>"off",'autofocus'=>'autofocus', 'onkeyup'=>"javascript:SearchMemberName(this.value);"]) ?>
         <div id="member_name_list">
            <ul>
              <li class="bg-gray"><ol class="wid-150">姓名</ol><ol class="wid-150">电话</ol><ol class="wid-230">详细地址</ol></li>
            </ul>
            <ul id="list">
            
            </ul>
       
         </div>

    </div>
    <div class="table_div">
        <?= $form->field($model, 'member_phone')->textInput(['maxlength' => true]) ?>
    </div><span id="modify" style=" text-decoration:underline; color:#999; cursor:pointer">修改</span>
</div>
<div class="table01">
    <div class="table_div">
    <?= $form->field($model, 'member_cityid')->dropDownList($area::getRegion(8), []) ?>
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
    <?= $form->field($model, 'receiving_provinceid')->dropDownList($area::getRegion(), ['value' => 8, 'disabled' => true]) ?>
    </div>
    <div id="search-form" data-search-value="<?= $areaName ?>" class="displayBlock"></div>
    <?= Html::input('hidden', 'LogisticsOrder[receiving_cityid]', $model->receiving_cityid ? $model->receiving_cityid : 130, ['id'=>'logisticsorder-receiving_cityid']) ?>
    <?= Html::input('hidden', 'LogisticsOrder[receiving_areaid]', $model->receiving_areaid ? $model->receiving_areaid : 0, ['id'=>'logisticsorder-receiving_areaid']) ?>
</div>
<div class="table01">
    <div class="area_div">
    <?= $form->field($model, 'receiving_name_area')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="table01">
	<div class="table_div">
    <?= $form->field($model, 'goods_num')->textInput() ?>
    </div>
    <div class="table_div">
        <?= $form->field($model, 'logistics_route_id')->dropDownList($logisticsRouteInfo,['prompt' => '请选择路线']);?>
    </div>
</div>
</div><!--relative end-->
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
    <div>
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
</div>
<div class="table01">
	<div class="table_div">
    <?= $form->field($model, 'shipping_type')->dropDownList(ShippingTpye::getShippingType()) ?>
    </div>
    <div class="table_div" style="margin-left:10px;">
    <?= $form->field($model, 'pay_for')->label('垫付')->dropDownList(['0'=>'否','1'=>'是']) ?>
    </div>
    <div class="table_div">
    <?= $form->field($model, 'freight')->textInput(['maxlength' => true]) ?>
    </div>
</div>
<div class="form-group field-info-goods_info">
    <div class="title_bg">
        <span>商品详细信息</span>
    </div>
    <div class="table01">
       <div class="table_div">商品名称:<input type="text"   class="form-control" name="GoodsInfo[name][]"></div>
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
        <input type="hidden" name="ismodify" id="ismodify"  value="0"><!--判断是否修改发货人信息,0为不修改1位修改,同时传递对应用户id-->
       <?= Html::Button('保存', ['class' => 'btn btn-success','id'=>'submitButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

