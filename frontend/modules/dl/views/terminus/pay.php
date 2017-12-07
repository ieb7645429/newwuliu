<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use frontend\modules\dl\assets\TerminusPayAsset;

TerminusPayAsset::register($this);

$this->title = '代收款管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <h3>发货付款</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>票号</th>
            <th>发货人</th>
            <th>收货人</th>
            <th>开单时间</th>
            <th>落地时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>代收款</th>
            <th>返货货值</th>
            <th>运费优惠价钱</th>
            <th>代收方式</th>
            <th>运费方式</th>
            <th>运费状态</th>
            <th>代收款状态</th>
            <th>应付</th>
            <th width="150">备注</th>
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
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['member_name'] ?></td>
          <td><?= $order['receiving_name'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['goods_price'] ?></td>
          <td><?= $order['return_goods_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['collection_name'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="goods_price_state_name_<?= $order['order_id']?>"><?= $order['goods_price_state_name'] ?></td>
          <td><?= $order['all_amount'] ?></td>
          <td class="remark" data-id="<?= $order['order_id']?>"><?= $order['terminus_content'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="15"><?= $all_amount ?></td>
        </tr>
        <tr>
          <td colspan="16">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
          </td>
        </tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
    
    <h3>退（返）货付款</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>物流单号</th>
            <th>发货人</th>
            <th>收货人</th>
            <th>开单时间</th>
            <th>入库时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>运费状态</th>
            <th>应付</th>
            <th width="150">备注</th>
          </tr>
        </thead>
        <?php 
            $return_all_amount = 0;
            $return_finished_amount = 0;
            $return_unfinished_amount = 0;
        ?>
        <?php if ($returnOrderList):?>
        <tbody>
        <?php foreach ($returnOrderList as $key => $order):?>
        <?php
            $return_all_amount += $order['all_amount'];
            $return_finished_amount += $order['finished_amount'];
            $return_unfinished_amount += $order['unfinished_amount'];
        ?>
        <tr>
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['member_name'] ?></td>
          <td><?= $order['receiving_name'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td class="return_freight_state_name_<?=$order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td><?= $order['all_amount'] ?></td>
          <td class="return_remark" data-id="<?= $order['order_id']?>"><?= $order['terminus_content'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="10"><?= $return_all_amount ?></td>
        </tr>
        <tr>
          <td colspan="11">
            <?= LinkPager::widget(['pagination' => $returnPages]); ?>
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
            <td>运费合计</td>
            <td>代收款合计</td>
          </tr>
          <tr>
            <td><?= $total['all_amount'] + $returnTotal['all_amount'] ?></td>
            <td><?= $total['freight_amount'] + $returnTotal['freight_amount'] ?></td>
            <td><?= $total['goods_amount'] + $returnTotal['goods_amount'] ?></td>
          </tr>
        </table>
    </div>
</div>
