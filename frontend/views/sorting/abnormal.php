<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\assets\SortingAbnormalAsset;
SortingAbnormalAsset::register($this);

$this->title = '异常订单';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(['action'=>['sorting/abnormal'],'method'=>'get'])?>
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
   <?= $form->field($return, 'member_cityid')->label('城市筛选')->dropDownList($cityList,['prompt' => '全部城市','options'=>[$city_id=>['Selected'=>true]]]) ?>
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
<?php ActiveForm::end()?>
<?php if(!empty($orderList)){?>
<div>总计:<?= $count;?>票</div>
    <table class="table tableTop tableTop10">
       
       <tbody>
       <thead>
              <tr class="tableBg">
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>收货地址</th>
                 <th>订单类型</th>
                 <th>退货城市</th>
                 <th>开单时间</th>
                 <th>类型</th>
                 <th>操作</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info">
          <?php if($value['checkbox']==1)$checkbox = true;else$checkbox = false;?>
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo $value['order_type_name']; ?></td>
             <td><?php echo $value['memberCity']['area_name']; ?></td>
             <td><?php echo date("Y-m-d H:i:s",$value['add_time']); ?></td>
             <td>
                 <?php if($value['same_city']==1){?>
                  <span class="finish">同城</span>
                 <?php }else{?>
                  <span class="finish">外埠</span>
                 <?php }?>
             </td>
             <td>
                  <span class="operation" data-order-id=<?php echo $value['order_id']?>>恢复</span>
             </td>
          </tr>
             <?php if(!empty($value['returnGoods'])){?>
          <tr>
             <td  class="goodsTableTd" colspan="10">
                        <table class="table">
                            <?php foreach($value['returnGoods'] as $k => $v){?>
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
