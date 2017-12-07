<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use frontend\modules\dl\assets\ReturnCompleteOkAsset;
ReturnCompleteOkAsset::register($this);

$this->title = '退货完成订单';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin()?>
    <?= $form->field($return, 'logistics_sn',['labelOptions' => ['label' => Yii::$app->params['logistics_sn']]])->textInput(['style'=>'width:50%']) ?>
    <?= $form->field($returnGoods, 'goods_sn',['labelOptions' => ['label' => Yii::$app->params['goods_sn']]])->textInput(['style'=>'width:50%']) ?>
    <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
<?php ActiveForm::end()?>
        
<?php if(!empty($orderList)){?>
    <table class="table tableTop tableTop50">
       
       <tbody>
       <thead>
              <tr class="tableBg">
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>收货地址</th>
                 <th>操作</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info table-tr-<?php echo $value['order_id']; ?>">
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td>
             <?php if($value['button']!=0):?>
                 <span class="operation" data-order-id="<?php echo $value['order_id']?>">确定</span>
             <?php endif;?>
             </td>
          </tr>
             <?php if(!empty($value['returnGoods'])){?>
          <tr class="table-tr-<?php echo $value['order_id']; ?>">
             <td  class="goodsTableTd" colspan="7">
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
<?php }?>
