<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\assets\TerminusReturnAsset;
TerminusReturnAsset::register($this);

$this->title = '落地点';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

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
<?php ActiveForm::end()?>
<div class="body-content">
    </div>
    <?= LinkPager::widget(['pagination' => $pages]); ?>
<?php if(!empty($orderList)){?>
    <table class="table tableTop">
       
       <tbody>
       <thead>
              <tr class="tableBg">
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>物流路线</th>
                 <th>收货地址</th>
                 <th>封车时间</th>
                 <th class="thCenter">操作</th>
                 <th class="thCenter">查看</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info table-tr-<?=$value['order_id']?>">
             <td><?= Html::a($value['logistics_sn'], Url::to(['employee/view','id'=>$value['order_id']]),['target' => '_blank']) ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['routeInfo']['logistics_route_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo date("Y-m-d H:i:s",$value['orderTime']['ruck_time']); ?></td>
             <td><span class="operation" data-order-id="<?php echo $value['order_id']?>">完成</span></td>
             <td>
                 <?= Html::a('<span class="finish">查看</span>', Url::to(['return/view','id'=>$value['return_id']]),['target' => '_blank']) ?>

             </td>
          </tr>
             <?php if(!empty($value['goodsInfo'])){?>
          <tr class="table-tr-<?=$value['order_id']?>">
             <td  class="goodsTableTd" colspan="10">
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
<?php }?>
