<?php

use yii\helpers\Html;
use backend\assets\TellerIncomeDriverDetailsAsset;
TellerIncomeDriverDetailsAsset::register($this);

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
            <th><?= Html::checkbox('check_all', false, ['id' => 'check_all']) ?></th>
            <th>司机</th>
            <th>票号</th>
            <th>运费</th>
            <th>制单费</th>
            <th>代收款</th>
            <th>运费优惠价钱</th>
            <th>代收方式</th>
            <th>运费方式</th>
            <th>运费状态</th>
            <th>代收款状态</th>
            <th>应收</th>
          </tr>
        </thead>
        <?php if ($orderList):?>
        <tbody>
        <?php foreach ($orderList as $key => $order):
		  if($order['is_confirm']):
		?>
        <tr>
          <td><?= Html::checkbox('order_id', false, ['class'=>'return_order_check order_check order_check_'.$order['order_id'], 'value' => $order['order_id']]) ?></td>
          <td><?= $order['object_name'] ?></td>
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['goods_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['collection_name'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="goods_price_state_name_<?= $order['order_id']?>"><?= $order['goods_price_state_name'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
        </tr>
        <?php
		 endif;
		 endforeach;?>
        <tr><td colspan="13">
<?= Html::button('打印', ['class' => 'btn btn-primary js-loading-print']) ?>

</td>
         
</tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
</div>
