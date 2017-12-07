<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use backend\assets\TellerIncomeDriverDetailsAsset;
TellerIncomeDriverDetailsAsset::register($this);

$this->title = '财务';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>

<?php
Modal::begin([
    'id' => 'remark-modal',
    'header' => '<h4 class="modal-title">订单备注</h4>',
]);?>
<div id="remark-modal-body">

</div>
<?php Modal::end();?>

<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search_details', ['model' => $searchModel]); ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th><?= Html::checkbox('check_all', false, ['id' => 'check_all']) ?></th>
            <th>司机账号</th>
            <th>司机姓名</th>
            <th>票号</th>
            <th>开单时间</th>
            <th>封车时间</th>
            <th>已提运费</th>
            <th>制单费</th>
            <th>代收款</th>
            <th>返货货值</th>
            <th>退/返货票号</th>
            <th>运费优惠价钱</th>
            <th>代收方式</th>
            <th>运费方式</th>
            <th>运费状态</th>
            <th>代收款状态</th>
            <th width="150">备注</th>
            <th>应收</th>
            <th></th>
          </tr>
        </thead>
        <?php 
            $all_amount = 0;
            $finished_amount = 0;
            $unfinished_amount = 0;
        ?>
        <?php if ($orderList):?>
        <tbody>
        <?php foreach ($orderList as $key => $order):?>
        <?php 
            $all_amount += $order['all_amount'];
            $finished_amount += $order['finished_amount'];
            $unfinished_amount += $order['unfinished_amount'];
        ?>
        <tr>
          <td><?= Html::checkbox('order_id', false, ['class'=>'order_check order_check_'.$order['order_id'], 'value' => $order['order_id'],'data-id' => empty($order['is_confirm'])?'0':$order['is_confirm']]) ?></td>
          <td><?= $order['object_name'] ?></td>
          <td><?= $order['object_truename'] ?></td>
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['ruck_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['goods_price'] ?></td>
          <td><?= $order['return_goods_price'] ?></td>
          <td><?= $order['return_logistics_sn'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['collection_name'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="goods_price_state_name_<?= $order['order_id']?>"><?= $order['goods_price_state_name'] ?></td>
          <td class="remark"><?= Html::button('查看修改备注', ['id'=>'remark_'.$order['order_id'],'class' => 'btn btn-info remark_edit','data-id'=>$order['order_id'],'data-toggle' => 'modal','data-target' => '#remark-modal'])?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
          <td>
            <?php if(!$order['is_confirm']):?>
            <?= Html::button('确认收款', ['id'=>'confirm-collection_'.$order['order_id'],'class' => 'btn btn-danger confirm-collection','data-order-id'=>$order['order_id'],'disabled'=>$order['is_confirm']]) ?>
            <?php if($order['collection'] == 1):?>
            <?= Html::button('垫付', ['id'=>'confirm-collection2_'.$order['order_id'],'class' => 'btn btn-warning confirm-collection','data-order-id'=>$order['order_id'],'data-advance'=>1,'disabled'=>$order['is_confirm']]) ?>
            <?php endif;?>
            <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="17"><?= $all_amount ?></td>
        </tr>
        <tr>
          <td colspan="18">
           <?= Html::button('全部确认收款', ['class' => 'btn btn-danger','id' => 'all-confirm-collection']) ?>
           <?= Html::button('全部垫付', ['class' => 'btn btn-warning','id' => 'all-confirm-collection2', 'data-advance'=>1]) ?>
           &nbsp;	
           <?= Html::button('打印', ['class' => 'btn btn-primary js-loading-print']) ?>
          </td>
        </tr>
        <tr>
          <td colspan="18">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
          </td>
        </tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
    
    <h3>总计</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <tr>
            <td>应收</td>
            <td>未收</td>
            <td>已收</td>
          </tr>
          <tr>
            <td><?= $total['all_amount']?></td>
            <td><?= $total['unfinished_amount']?></td>
            <td><?= $total['finished_amount']?></td>
          </tr>
        </table>
    </div>
</div>
