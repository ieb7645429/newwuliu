<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\assets\TerminusListAsset;
use yii\helpers\Url;
TerminusListAsset::register($this);

$this->title = '订单详情';
$this->params['breadcrumbs'][] = ['label' => '路上车辆', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<h1><?=$this->title ?></h1>
<?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($LogisticsOrder, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $params['logistics_sn']]) ?>
    <?= $form->field($goods, 'goods_sn',['labelOptions' => ['label' => Yii::$app->params['goods_sn']]])->textInput(['value' => $params['goods_sn']]) ?>
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
    <?php echo Html::button('落地打印', ['class'=>'btn btn-primary js-print']) ?>
<?php ActiveForm::end()?>
<input type="hidden" id="driver_id" value="<?php echo $driver_id?>">
<input type="hidden" id="count_js" value="<?=$count;?>">
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
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>物流路线</th>
                 <th>收货地址</th>
                 <th>封车时间</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info">
          <td><?= Html::checkbox('print',in_array($value['order_id'],$order_arr)||!isset($_GET['page'])?true:false,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td>
             <?= Html::a($value['logistics_sn'], Url::to(['employee/view','id'=>$value['order_id']]),['target' => '_blank']) ?>
             </td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['routeInfo']['logistics_route_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo date("Y-m-d H:i:s",$value['orderTime']['ruck_time']); ?></td>
          </tr>
             <?php if(!empty($value['goodsInfo'])){?>
          <tr>
             <td  class="goodsTableTd" colspan="8">
                        <table class="table">
                            <?php foreach($value['goodsInfo'] as $k => $v){?>
                            <tr class="goodsTableTr">
                                <td><?=Yii::$app->params['goods_sn']?>:<?php echo $v['goods_sn'];?></td>
                                
                                <td>
                                    <?php if($v['buttonType']==2){?>
                                    <span class="lose" data-goods-id="<?php echo $v['goods_id']?>">异常</span>
                                    <?php }elseif($v['buttonType']==1){?>
                                    <span class="operation" data-goods-id="<?php echo $v['goods_id']?>">挂起</span>
                                    <?php }?>
                                    
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

