<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '代收款管理';
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
            <th>司机</th>
            <th>票号</th>
            <th>已提运费</th>
            <th>制单费</th>
            <th>代收款</th>
            <th>返货货值</th>
            <th>运费优惠价钱</th>
            <th>代收方式</th>
            <th>运费方式</th>
            <th>运费状态</th>
            <th>代收款状态</th>
            <th>应付</th>
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
          <td><?= $order['object_name'] ?></td>
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['goods_price'] ?></td>
          <td><?= $order['return_goods_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['collection_name'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="goods_price_state_name_<?= $order['order_id']?>"><?= $order['goods_price_state_name'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="13"><?= $all_amount ?></td>
        </tr>
        <tr>
          <td colspan="14">
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
            <td>应付</td>
          </tr>
          <tr>
            <td><?= $total['all_amount']?></td>
          </tr>
        </table>
    </div>
</div>
