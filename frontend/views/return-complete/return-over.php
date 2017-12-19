<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use frontend\assets\ReturnCompleteOverAsset;
use yii\widgets\LinkPager;
ReturnCompleteOverAsset::register($this);

$this->title = '退货完成订单';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(['action'=>['return-complete/return-over'],'method'=>'get'])?>
    <?= $form->field($return, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $params['logistics_sn']]) ?>
    
    <?= $form->field($return, 'add_time')->label('送货时间')->widget(DateRangePicker::classname(), [
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
              <th width="80px"><?= Html::checkbox('all',false,['style'=>'margin-right:5px','id'=>'check_all']);?>打印</th>
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>运费</th>
                 <th>付款方式</th>
                 <th>发货人</th>
                 <th>发货人市</th>
                 <th>发货人电话</th>
                 <th>收货人</th>
                 <th>收货人电话</th>
                 <th>收货地址</th>
                 <th>开单时间</th>
                 <th>送货时间</th>
                 <th>送货人</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
          <tr class="info">
          <td><?= Html::checkbox('print',in_array($value['order_id'],$order_arr)?true:false,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['freight']; ?></td>
             <td><?php echo $value['shippingType']['name']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['memberCity']['area_name']; ?></td>
             <td><?php echo $value['member_phone']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['receiving_phone']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo empty($value['add_time'])?'':date("Y-m-d H:i:s",$value['add_time']); ?></td>
             <td><?php echo empty($value['returnOrderTime']['collection_time'])?'':date("Y-m-d H:i:s",$value['returnOrderTime']['collection_time']); ?></td>
             <td><?php echo $value['sender']?></td>
          </tr>
             <?php if(!empty($value['returnGoods'])){?>
          <tr>
             <td  class="goodsTableTd" colspan="13">
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
