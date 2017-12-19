<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use common\models\UserAll;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\assets\DriverCityWideAsset;
DriverCityWideAsset::register($this);

$this->title = '封车';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<?php $form = ActiveForm::begin(['action'=>[$rule],'method'=>'get'])?>
    <?= $form->field($LogisticsOrder, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $params['logistics_sn']]) ?>
   <?= $form->field($LogisticsOrder, 'employee_id')->label('开单员')->dropDownList($emoloyeeList,['prompt' => '请选择','options'=>[$params['employee_id']=>['Selected'=>true]]]) ?>
   <?= $form->field($LogisticsOrder, 'order_type')->label('订单类型')->dropDownList($orderTypeList,['prompt' => '请选择','options'=>[$params['order_type']=>['Selected'=>true]]]) ?>
    <?= $form->field($LogisticsOrder, 'add_time')->label('时间分类')->dropDownList(['1'=>'封车时间', '2'=>'开单时间'],['value'=>$params['time_type']])?>
    <?= $form->field($orderTime, 'ruck_time')->label('查询时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$LogisticsOrder,
            'options' => [
                'class' => 'form-control',
                'value' => $add_time ? $add_time['date'] : '' ,
            ],
            'pluginOptions'=>[
                'timePicker'=>true,
                'timePickerIncrement'=>5,
                'locale'=>['format'=>'Y-m-d H:i:s', 'separator'=>' - ',]
            ]
   ])?>
    <?= $form->field($orderPrintLog, 'terminus')->dropDownList([''=>'全部', '2'=>'未打印', '1'=>'已打印'], ['value'=>$params['print']])->label('是否打印') ?>
    <div class="form-group">
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
    <?php echo Html::button('打印送货单(正)', ['class'=>'btn btn-primary js-print-z']) ?>
    <?php echo Html::button('打印小码单', ['class'=>'btn btn-primary js-small-print']) ?>
    <div class="div-print">小码单全部打印<input id="checkbox-input" class="checkbox-input" type="checkbox" <?php if($is_print==1) echo 'checked';?>></div>
	</div>
<?php ActiveForm::end()?>
<div class="body-content">
<input type="hidden" id="count_js" value="<?=$count;?>">
    </div>
    <?= LinkPager::widget(['pagination' => $pages]); ?>
<?php if(!empty($orderList)){?>

    <table class="table tableTop">
       <tbody>
       <thead>
          <tr class="row">
              <div > 当前已选中<strong id="count"><?=$count;?></strong>项</div>
          </tr>
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
                 <th>开单员</th>
                 <th>封车时间</th>
                 <th>是否打印</th>
                 <th class="tdMiddle">操作</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info goodsTableTr_<?= $value['order_id']?>">
          <td><?= Html::checkbox('print',in_array($value['order_id'],$order_arr)||!isset($_GET['page'])?true:false,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php if($value['order_type']==1) echo '西部';if($value['order_type']==3) echo '瑞胜';if($value['order_type']==4) echo '塔湾';?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['routeInfo']['logistics_route_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo empty($value['add_time'])?'':date("Y-m-d H:i:s",$value['add_time']); ?></td>
             <td><?php echo UserAll::findOne($value['employee_id'])->username?></td>
             <td><?php echo empty($value['orderTime']['ruck_time'])?'':date("Y-m-d H:i:s",$value['orderTime']['ruck_time']); ?></td>
             <td id="print_log_<?=$value['order_id']?>">
               <?php if ($value['orderPrintLog'] && $value['orderPrintLog']['terminus'] == 1) {
                        echo '已打印';
                     } else {
                         echo '未打印';
                     } 
               ?>
             </td>
             <td>
                 <?php if($value['stateButtonType']==1):?>
                 <span class="operation" data-order-id="<?php echo $value['order_id']?>">确认</span>
                 <?php endif?>
                 <?php if($value['stateButtonType']==2):?>
                     <span class="finish">已确认</span>
                 <?php endif?>
             </td>
          </tr>
             <?php if(!empty($value['goodsInfo'])){?>
          <tr class="goodsTableTr_<?= $value['order_id']?>">
             <td  class="goodsTableTd" colspan="13">
                        <table class="table">
                            <?php foreach($value['goodsInfo'] as $k => $v){?>
                            <tr>
                                <td><?=Yii::$app->params['goods_sn']?>:<?php echo $v['goods_sn'];?></td>
                                <td>所在车辆&nbsp;:&nbsp;<?php echo $v['carInfo']['car_number'];?></td>
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
