<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use common\models\UserAll;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\assets\DriverAsset;
DriverAsset::register($this);

$this->title = '封车';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(['action'=>['driver/myself'],'method'=>'get'])?>
    <?= $form->field($LogisticsOrder, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $params['logistics_sn']]) ?>
    <?= $form->field($LogisticsOrder, 'employee_id')->label('开单员')->dropDownList($emoloyeeList,['prompt' => '请选择','options'=>[$params['employee_id']=>['Selected'=>true]]]) ?>
   <?= $form->field($LogisticsOrder, 'order_type')->label('订单类型')->dropDownList($orderTypeList,['prompt' => '请选择','options'=>[$params['order_type']=>['Selected'=>true]]]) ?>
    <?= $form->field($LogisticsOrder, 'add_time')->label('时间分类')->dropDownList(['1'=>'封车时间', '2'=>'开单时间'],['value'=>$params['time_type']])?>
    <?= $form->field($orderTime, 'ruck_time')->label('封车时间')->widget(DateRangePicker::classname(), [
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
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
    <?php echo Html::button('打印', ['class'=>'btn btn-primary js-print']) ?>
<?php ActiveForm::end()?>
<div class="body-content">
<input type="hidden" id="count_js" value="<?=$count;?>">
    </div>
    <?= LinkPager::widget(['pagination' => $pages]); ?>
<?php if(!empty($orderList)){?>
    <table class="table tableTop">
       
       <tbody>
       <thead>
              <tr class="tableBg">
              <th width="80px"><?= Html::checkbox('all',false,['style'=>'margin-right:5px','id'=>'check_all']);?>全选</th>
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
                 <th class="tdMiddle">类型</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info">
          <td><?= Html::checkbox('print',in_array($value['order_id'],$order_arr)?true:false,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
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
             <td>
                <?php if($value['same_city']==1){?>
                  <span class="finish">同城</span>
                 <?php }else{?>
                  <span class="finish">外埠</span>
                 <?php }?>
              </td>
          </tr>
             <?php if(!empty($value['goodsInfo'])){?>
          <tr>
             <td  class="goodsTableTd" colspan="12">
                        <table class="table">
                            <?php foreach($value['goodsInfo'] as $k => $v){?>
                            <tr class="goodsTableTr">
                                <td><?=Yii::$app->params['goods_sn']?>:<?php echo $v['goods_sn'];?></td>
                                <td>所在车辆&nbsp;:&nbsp;<?php echo $v['carInfo']['car_number'];?></td>
                                <td></td>
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
