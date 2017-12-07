<?php 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\assets\BalanceEditAsset;
BalanceEditAsset::register($this);
use components\autocomplete\AutoCompltetAsset;
AutoCompltetAsset::register($this);

$this->title = '价格修改';
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(); ?>
    <?= $form->field($model, 'goods_price',['labelOptions' => ['label' => '当前代收款']])->textInput(['maxlength' => true,'style'=>'width:50%','disabled'=>'disabled']) ?>
<div class="title_bg">
<span>订单信息</span>
</div>
    <?php if(!empty($model->order_sn)):
        if(!is_numeric($model->order_sn)){
            $ordersn = explode(',',unserialize($model->order_sn));
        }else{
            $ordersn = explode(',',$model->order_sn);
        }
	    foreach($ordersn as $key => $val):
	 ?>
     <div class="table01">
     <div class="table_div"><label>订单编号:</label><input value="<?=$val?>" type="text"   list="sn_list" id="logisticsorder-order_sn" class="form-control sn" name="OrderSn[]" onkeyup="javascript:SearchSn(this.value);" disabled="disabled"></div>
     <datalist id="sn_list" >
     </datalist>
     </div>
     <?php
	  endforeach;
	  else:
	  ?>
	  <div class="table01">
    <div class="table_div"><label>订单编号:</label><input  type="text"   list="sn_list" id="logisticsorder-order_sn" class="form-control" onblur="javascript:GetValue(this.value);" name="OrderSn[]" onkeyup="javascript:SearchSn(this.value);"></div>
     <datalist id="sn_list" >
     </datalist>
     </div>
        <a id="logisticsorder-add_order_sn" href="" class="btn btn-success add-button" onclick="return false;">添加订单编号</a>
    <?php endif;?>
<div class="title_bg">
<span>发货信息</span>
</div>
<div class="table01">
	<div class="table_div">
    <?php if($model->collection==2){?>
    <?= $form->field($order, 'goods_price',['labelOptions' => ['label' => '代收款修改']])->textInput(['maxlength' => true,'id'=>'true_price','style'=>'width:50%','readonly'=>'readonly','value' => intval($model->goods_price)]) ?>
    <?php }else{?>
    <?= $form->field($order, 'goods_price',['labelOptions' => ['label' => '代收款修改']])->textInput(['maxlength' => true,'id'=>'true_price','style'=>'width:50%','value' => intval($model->goods_price)]) ?>
    <?php }?>
    </div>
</div>
<div class="title_bg">
<span>收货信息</span>
</div>
<!--     路线修改 -->
<div class="table01">
	<?= Html::input('hidden', 'LogisticsOrder[receiving_cityid]', $model->receiving_cityid ? $model->receiving_cityid : 107, ['id'=>'logisticsorder-receiving_cityid']) ?>
    <?= Html::input('hidden', 'LogisticsOrder[receiving_areaid]', $model->receiving_areaid ? $model->receiving_areaid : 0, ['id'=>'logisticsorder-receiving_areaid']) ?>
	<div id="search-form" data-search-value="<?= $areaName ?>" class="displayBlock"></div>
    <?= $form->field($model, 'logistics_route_id')->dropDownList($logisticsRouteInfo);?>
    <div class="table_div terminusSelect <?php if($this->context->action->id=='create'||$model->same_city==1):?>terminus_list<?php endif;?>">
        <?= $form->field($model, 'terminus_id')->dropDownList($terminus, $this->context->action->id=='create'||$model->same_city==1?['disabled' =>'true']:[]);?>
    </div>
</div>
<div class="title_bg">
<span>运费信息</span>
</div>
<div class="table01">
    <div class="table_div">
    	<?= $form->field($order, 'freight',['labelOptions' => ['label' => '运费']])->textInput(['maxlength' => true,'style'=>'width:50%','value' => intval($model->freight)]) ?>
    </div>
    <div class="table_div">
        <?= $form->field($order, 'make_from_price',['labelOptions' => ['label' => '制单费']])->textInput(['maxlength' => true,'style'=>'width:50%','value' => intval($model->make_from_price)]) ?>
    </div>
    <div class="table_div">
        <?= $form->field($order, 'collection')->dropDownList(array('1'=>'代收','2'=>'不代收'),['style'=>'width:50%','options'=>[$model->collection=>['Selected'=>true]]]);?>
    </div>
</div>
<div class="title_bg">
<span>其他信息</span>
</div>
<div class="table01">
	<div class="table_div">
    	<?= $form->field($order, 'abnormal')->dropDownList(array('1'=>'异常','2'=>'正常'),['style'=>'width:50%','options'=>[$model->abnormal=>['Selected'=>true]]])->label("是否异常");?>
    </div>
    <div class="table_area">
    	<?= $form->field($orderRemark, 'edit_content',['labelOptions' => ['label' => '备注']])->textarea(['rows'=>5,'style'=>'width:50%','value'=>empty($markModel->edit_content)?'':$markModel->edit_content])?>
	</div>
</div>
<?php 
    $confirm = '是否修改此订单?';
if($model->order_state>=50){
    if($model->order_state==70&&($model->state==5||$model->state==6)){
        $confirm = '订单已完成,确定是否修改此订单?';
    }else{
        $confirm = '订单已封车,确定是否修改此订单?';
    }
}?>
    <?= Html::submitButton('保存', ['class' => 'btn btn-success','data'=>['confirm'=>$confirm,]]) ?>
<?php ActiveForm::end(); ?>
