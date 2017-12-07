<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use backend\modules\dl\assets\TellerReturnIncomeDealerDetailsAsset;
TellerReturnIncomeDealerDetailsAsset::register($this);

$this->title = '财务';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_return-search_details', ['model' => $searchModel]); ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th><?= Html::checkbox('check_all', false, ['id' => 'check_all']) ?></th>
            <th>退货员</th>
            <th>票号</th>
            <th>开单时间</th>
            <th>入库时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>运费状态</th>
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
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="return_remark" data-id="<?= $order['order_id']?>"><?= $order['content'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
          <td>
            <?php if(!$order['is_confirm']):?>
            <?= Html::button('确认收款', ['id'=>'confirm-collection_'.$order['order_id'],'class' => 'btn btn-danger confirm-collection','data-order-id'=>$order['order_id'],'disabled'=>$order['is_confirm']]) ?>
            <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="11"><?= $all_amount ?></td>
        </tr>
        <tr><td colspan="12">
		<?= Html::button('全部确认收款', ['class' => 'btn btn-danger','id' => 'all-confirm-collection']) ?>
        &nbsp;
        <?= Html::button('打印', ['class' => 'btn btn-primary js-loading-print']) ?>
        </td></tr>
        <tr>
          <td colspan="12">
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
