<?php

use yii\helpers\Html;
use backend\assets\TellerReturnIncomeEmployeeDetailsAsset;
TellerReturnIncomeEmployeeDetailsAsset::register($this);
$this->title = '打印列表';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th><?= Html::checkbox('order_id[]', false,['id'=>'return_check_all']) ?></th>
            <th>开单员</th>
            <th>票号</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>运费状态</th>
            <th>应收</th>
          </tr>
        </thead>
        <?php
		 if($status=='return'):
		   if ($orderList):
	    ?>
        <tbody>
        <?php foreach ($orderList as $key => $order):
		 if($order['is_confirm']):
		?>
        <tr>
          <td><?= Html::checkbox('order_id', false, ['value' => $order['order_id'],'class'=>'return_order_check order_check order_check_'.$order['order_id']]) ?></td>
          <td><?= $order['object_name'] ?></td>
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td class="freight_state_name_<?=$order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
        </tr>
        <?php
		endif;
		 endforeach;?>
        <tr><td colspan="9">
           <?= Html::button('打印', ['class' => 'btn btn-primary js-loading-print']) ?>
        </td></tr>
        </tbody>
        <?php 
		  endif;
		endif;
		?>
        <?php
		 if($status=='send'):
		   if ($orderList):
	    ?>
        <tbody>
        <?php foreach ($orderList as $key => $order):
		?>
        <tr>
          <td><?= Html::checkbox('order_id', false, ['value' => $order['order_id'],'class'=>'return_order_check order_check order_check_'.$order['order_id']]) ?></td>
          <td><?= $order['object_name'] ?></td>
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td class="freight_state_name_<?=$order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
        </tr>
        <?php
		 endforeach;?>
        <tr><td colspan="9">
           <?= Html::button('打印', ['class' => 'btn btn-primary js-loading-print']) ?>
        </td></tr>
        </tbody>
        <?php 
		  endif;
		endif;
		?>
      </table>
    </div>
</div>
