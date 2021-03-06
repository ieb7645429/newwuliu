<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use frontend\modules\dl\assets\ReturnCompleteOverAsset;
use yii\widgets\LinkPager;
ReturnCompleteOverAsset::register($this);

$this->title = '退货完成订单';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(['method'=>'get'])?>
    <?= $form->field($return, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['value' => $order_sn]) ?>
    <?= $form->field($returnGoods, 'goods_sn',['labelOptions' => ['label' => Yii::$app->params['goods_sn']]])->textInput(['value' => $goods_sn]) ?>
    <?= $form->field($return, 'add_time')->label('送货时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$return,
            'options' => [
                'class' => 'form-control',
                'value' => $add_time ? $add_time['date'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
   ])?>
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
<?php ActiveForm::end()?>
        
<?php if(!empty($orderList)){?>
    <table class="table tableTop tableTop50">
       
       <tbody>
       <thead>
              <tr class="tableBg">
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
