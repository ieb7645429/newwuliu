<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\widgets\LinkPager;
use frontend\assets\InstockHistoryAsset;
InstockHistoryAsset::register($this);

$this->title = '退货列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<?php $form = ActiveForm::begin(['action'=>['instock/history-list'],'method'=>'get'])?>
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
    <?= Html::button('退货打印', ['class'=>'btn btn-primary js-print'])?>
<?php ActiveForm::end()?>
        
<?php if(!empty($orderList)){?>
<?= LinkPager::widget(['pagination' => $pages]); ?>

<input type="hidden" id="count_js" value="<?=$check_count;?>">
    <table class="table tableTop tableTop10">
       
       <tbody>
       <thead>
       <tr class="row">
       <div>总计:<?= $count;?>票</div>
              <div > 当前已选中<strong id="count"><?=$check_count;?></strong>项</div>
          </tr>
              <tr class="tableBg">
              <th width="80px"><?= Html::checkbox('all',true,['style'=>'margin-right:5px','id'=>'check_all']);?>全选</th>
                 <th><?=Yii::$app->params['logistics_sn']?></th>
                 <th>代收款</th>
                 <th>发货人</th>
                 <th>收货人</th>
                 <th>收货地址</th>
                 <th>退货城市</th>
                 <th>开单时间</th>
                 <th>类型</th>
              </tr>
           </thead>
       <?php 
            foreach($orderList as $key=>$value){
        ?>
            
          <tr class="info">
          <td><?= Html::checkbox('print',in_array($value['order_id'],$order_arr)||!isset($_GET['page'])?true:false,['class'=>'order_check checkbox'.$value['order_id'],'value' => $value['order_id']]);?></td>
             <td><?php echo $value['logistics_sn']; ?></td>
             <td><?php echo $value['goods_price']; ?></td>
             <td><?php echo $value['member_name']; ?></td>
             <td><?php echo $value['receiving_name']; ?></td>
             <td><?php echo $value['receiving_name_area']; ?></td>
             <td><?php echo $value['memberCity']['area_name']; ?></td>
             <td><?php echo date("Y-m-d H:i:s",$value['add_time']); ?></td>
             <td>
                 <?php if($value['same_city']==1){?>
                  <span class="finish">同城</span>
                 <?php }else{?>
                  <span class="finish">外埠</span>
                 <?php }?>
             </td>
          </tr>
             <?php if(!empty($value['returnGoods'])){?>
          <tr>
             <td  class="goodsTableTd" colspan="9">
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
