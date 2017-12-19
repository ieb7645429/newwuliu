<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use frontend\assets\ReturnCompleteAsset;
ReturnCompleteAsset::register($this);

$this->title = '退货处理列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;

Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title">填写送货人</h4>',
]);
?>
<div class="payDiv">
    <div class="payInput">
    	<?=Html::input('text','sender','',['id'=>'sender','class' => 'form-control pay-input']);?>
    	<?=Html::input('hidden','order_id','',['id'=>'order_id','class' => 'form-control pay-input']);?>
    </div>
    <div class="row payButtonDiv">
    	<div class="col-md-6 payButton"><?php echo Html::button('确定', ['id'=>'confirm','class'=>'btn btn-primary']);?></div>
    	<div class="col-md-6 payButton"><?php echo Html::button('取消', ['class'=>'btn btn-primary','data-dismiss'=>'modal']);?></div>
    </div>
</div>
<?php Modal::end();?>
<?php $form = ActiveForm::begin(['action'=>['return-complete/index'],'method'=>'get'])?>
    <?= $form->field($return, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $params['logistics_sn']]) ?>
    <?= $form->field($return, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$return,
            'options' => [
                'class' => 'form-control',
                'value' => $add_time ? $add_time['date'] : '' ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
   ])?>
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
    <?php echo Html::button('打印', ['class'=>'btn btn-primary js-print']) ?>
<?php ActiveForm::end()?>
<input type="hidden" id="count_js" value="<?=$check_count;?>">    
<?php if(!empty($orderList)){?>
<?= LinkPager::widget(['pagination' => $pages]); ?>
    <table class="table tableTop tableTop">
       <tr class="row">
              <div > 当前已选中<strong id="count"><?=$check_count;?></strong>项</div>
          </tr>
       <tbody>
       <thead>
              <tr class="tableBg">
              <th width="80px"><?= Html::checkbox('all',true,['style'=>'margin-right:5px','id'=>'check_all']);?>打印</th>
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>发货人市</th>
                 <th>收货人</th>
                 <th>收货地址</th>
                 <th>入库时间</th>
                 <th>操作</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info tr-order-<?php echo $value['order_id'];?>">
          <td><?= Html::checkbox('print',in_array($value['order_id'],$order_arr)||!isset($_GET['page'])?true:false,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['memberCity']['area_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo empty($value['returnOrderTime']['ruck_time'])?'':date("Y-m-d H:i:s",$value['returnOrderTime']['ruck_time']); ?></td>
             <td>
             <?php if($identity!=0):?>
                 <span class="operation" data-toggle = 'modal' data-target = '#create-modal' data-order-id="<?php echo $value['order_id']?>">送货</span>
             <?php endif;?>
             </td>
          </tr>
             <?php if(!empty($value['returnGoods'])){?>
          <tr class="tr-order-<?php echo $value['order_id'];?>">
             <td  class="goodsTableTd" colspan="9">
                        <table class="table">
                            <?php foreach($value['returnGoods'] as $k => $v){?>
                            <tr class="goodsTableTr">
                                <td><?=Yii::$app->params['goods_sn']?>:<?php echo $v['goods_sn'];?></td>
                            </tr>
                            <?php } ?>
                        </table>
               </td>
           </tr>
             <?php } ?>
        <?php }?>
       </tbody>
    </table>
    <?= LinkPager::widget(['pagination' => $pages]); ?>
<?php }?>
