<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\modules\dl\assets\DriverAsset;
DriverAsset::register($this);

$this->title = '封车';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<?php $form = ActiveForm::begin(['method'=>'get'])?>
   <?= $form->field($LogisticsOrder, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $params['logistics_sn']]) ?>
   <?= $form->field($goods, 'goods_sn',['labelOptions' => ['label' => Yii::$app->params['goods_sn']]])->textInput(['value' => $params['goods_sn']]) ?>
   <?= $form->field($LogisticsOrder, 'add_time')->label('开单时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$LogisticsOrder,
            'options' => [
                'class' => 'form-control',
                'value' => $add_time ? $add_time['date'] : date('Y-m-d H:i:s') .' - ' . date('Y-m-d H:i:s') ,
            ],
            'pluginOptions'=>[
                'timePicker'=>true,
                'timePickerIncrement'=>5,
                'locale'=>['format'=>'Y-m-d H:i:s', 'separator'=>' - ',]
            ]
   ])?>
   <?= $form->field($route, 'logistics_route_id')->label('选择路线')->dropDownList($routeList,['prompt' => '请选择路线','options'=>[$params['route_id']=>['Selected'=>true]]]) ?>
    <?= $form->field($LogisticsOrder, 'driver_member_id')->label('选择司机')->dropDownList($driverList,['prompt' => '请选司机','options'=>[$params['driver_member_id']=>['Selected'=>true]]]) ?>
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
    <?php //echo Html::button('封车打印', ['class'=>'btn btn-primary js-loading-print']) ?>
<?php ActiveForm::end()?>
<div class="body-content">
    </div>
<?php if(!empty($orderList)){?>
    <table class="table tableTop">
       
       <tbody>
       <thead>
              <tr class="tableBg">
              <th width="80px"><?= Html::checkbox('all',true,['style'=>'margin-right:5px','id'=>'check_all']);?>全选</th>
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>订单类型</th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>物流路线</th>
                 <th>收货地址</th>
                 <th>开单时间</th>
                 <th>封车数量</th>
                 <th class="tdMiddle">类型</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info">
          <td><?= Html::checkbox('print',true,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php if($value['order_type']==1) echo '西部';if($value['order_type']==3) echo '瑞胜'; ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['routeInfo']['logistics_route_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo empty($value['add_time'])?'':date("Y-m-d H:i:s",$value['add_time']); ?></td>
             <td><span class="attention"><?=count($value['driverManagerGoods'])?>/<?=$value['goods_num']?></span></td>
             <td>
                 <?php if($value['same_city']==1){?>
                  <span class="finish">同城</span>
                 <?php }else{?>
                  <span class="finish">外埠</span>
                 <?php }?>
             </td>
          </tr>
             <?php if(!empty($value['driverManagerGoods'])){?>
          <tr>
             <td  class="goodsTableTd" colspan="11">
                        <table class="table">
                            <?php foreach($value['driverManagerGoods'] as $k => $v){?>
                            <tr class="goodsTableTr">
                                <td><?=Yii::$app->params['goods_sn']?>:<?php echo $v['goods_sn'];?></td>
                                <td>所在车辆&nbsp;:&nbsp;<?php echo $v['carInfo']['car_number'];?></td>
                                <td>
                                <span class="finish" data-order-id=<?php echo $value['order_id']?> data-goods-id="<?php echo $v['goods_id']?>">已处理</span>
                                </td>
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
    <div><?php echo Html::button('提交', ['class'=>'btn btn-primary js-submit']) ?></div>
<?php }?>




<?php $js = <<<JS
    $(function(){
	$('#logisticsroute-logistics_route_id').change(function(){
        var data = {
            'route_id':$(this).val()
        };
        $.ajax({
             type: "post",
             url:'?r=dl/driver-manager/ajax-get-driver-list',
             data:data,
             async:true,
             success:function(data){
                 $('#logisticsorder-driver_member_id option:not(:first)').remove();
                 $('#logisticsorder-driver_member_id').append(data);
              }
            })
    	})	
    })
        
JS;
$this->registerJs($js);
?>
