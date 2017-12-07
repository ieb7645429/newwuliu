<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\modules\dl\assets\TerminusMyselfAsset;
TerminusMyselfAsset::register($this);

$this->title = '落地点';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($LogisticsOrder, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $order_sn]) ?>
    <?= $form->field($goods, 'goods_sn',['labelOptions' => ['label' => Yii::$app->params['goods_sn']]])->textInput(['value' => $goods_sn]) ?>
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
   <?= $form->field($orderPrintLog, 'terminus')->dropDownList([''=>'全部', '2'=>'未打印', '1'=>'已打印'], ['value'=>Yii::$app->request->get('OrderPrintLog')['terminus']])->label('是否打印') ?>
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
    <?php echo Html::button('打印', ['class'=>'btn btn-primary js-print-other']) ?>
     <?php echo Html::button('打印送货单(正)', ['class'=>'btn btn-primary js-print-other-z']) ?>
<?php ActiveForm::end()?>
<div class="body-content">
<input type="hidden" class="orderSn" value="<?php echo $order_sn?>">
<input type="hidden" class="goodsSn" value="<?php echo $goods_sn?>">
    </div>
    <?= LinkPager::widget(['pagination' => $pages]); ?>
<?php if(!empty($orderList)){?>
    <table class="table tableTop">
       <tbody>
       <thead>
              <tr class="tableBg">
              <th width="80px"><?= Html::checkbox('all',true,['style'=>'margin-right:5px','id'=>'check_all']);?>全选</th>
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>物流路线</th>
                 <th>收货地址</th>
                 <th>封车时间</th>
                 <th>是否打印</th>
                 <th class="thCenter">操作</th>
                 <th class="thCenter">退货</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info table-tr-<?=$value['order_id']?>">
          <td><?= Html::checkbox('print',true,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td><?= Html::a($value['logistics_sn'], Url::to(['employee/view','id'=>$value['order_id']]),['target' => '_blank']) ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['routeInfo']['logistics_route_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo date("Y-m-d H:i:s",$value['orderTime']['ruck_time']); ?></td>
             <td id="print_log_<?=$value['order_id']?>">
               <?php if ($value['orderPrintLog'] && $value['orderPrintLog']['terminus'] == 1) {
                        echo '已打印';
                     } else {
                         echo '未打印';
                     } 
               ?>
             </td>
             <td>
                 <span class="operation" data-order-id="<?php echo $value['order_id']?>">完成</span>
             </td>
             <td class="go-return-<?= $value['order_id']?>">
             <?php if($value['returnButton']==1&&$value['collection']==1&&empty($value['return_logistics_sn'])):?>
                 <a href="?r=return/create&order_id=<?php echo $value['order_id']?>"><span class="lose">原返</span></a>
             <?php endif;?>
             </td>
          </tr>
             <?php if(!empty($value['goodsInfo'])){?>
          <tr class="table-tr-<?=$value['order_id']?>">
             <td  class="goodsTableTd" colspan="11">
                        <table class="table">
                            <?php foreach($value['goodsInfo'] as $k => $v){?>
                            <tr class="goodsTableTr">
                                <td><?=Yii::$app->params['goods_sn']?>:<?php echo $v['goods_sn'];?></td>
                               
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
    <div><?php echo Html::button('批量完成', ['class'=>'btn btn-primary','id'=>'all-over']) ?></div>
<?php }?>
