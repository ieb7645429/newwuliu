<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '财务';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <h3>发货运费</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>开单员</th>
            <th>已付运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>应收</th>
            <th>未收</th>
            <th>已收</th>
          </tr>
        </thead>
        <?php 
            $freight = 0;
            $make_from_price = 0;
            $shipping_sale = 0;
            $all_amount = 0;
            $finished_amount = 0;
            $unfinished_amount = 0;
        ?>
        <?php if ($orderList):?>
        <tbody>
        <?php foreach ($orderList as $key => $order):?>
        <?php
            $freight += $order['freight'];
            $make_from_price += $order['make_from_price'];
            $shipping_sale += $order['shipping_sale'];
            $all_amount += $order['all_amount'];
            $finished_amount += $order['finished_amount'];
            $unfinished_amount += $order['unfinished_amount'];
        ?>
        <tr>
          <td><?= Html::a($order['object_name'], Url::to($order['object_url_parameter'], true)) ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['all_amount'] ?></td>
          <td><?= $order['unfinished_amount'] ?></td>
          <td><?= $order['finished_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计</td>
          <td><?= $freight?></td>
          <td><?= $make_from_price?></td>
          <td><?= $shipping_sale?></td>
          <td><?= $all_amount?></td>
          <td><?= $unfinished_amount?></td>
          <td><?= $finished_amount?></td>
        </tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
    
    <h3>退（返）货运费</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>开单员</th>
            <th>已付运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>应收</th>
            <th>未收</th>
            <th>已收</th>
          </tr>
        </thead>
        <?php 
            $return_freight = 0;
            $return_make_from_price = 0;
            $return_shipping_sale = 0;
            $return_all_amount = 0;
            $return_finished_amount = 0;
            $return_unfinished_amount = 0;
        ?>
        <?php if ($returnOrderList):?>
        <tbody>
        <?php foreach ($returnOrderList as $key => $order):?>
        <?php 
            $return_freight += $order['freight'];
            $return_make_from_price += $order['make_from_price'];
            $return_shipping_sale += $order['shipping_sale'];
            $return_all_amount += $order['all_amount'];
            $return_finished_amount += $order['finished_amount'];
            $return_unfinished_amount += $order['unfinished_amount'];
        ?>
        <tr>
          <td><?= Html::a($order['object_name'], Url::to($order['object_url_parameter'], true)) ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['all_amount'] ?></td>
          <td><?= $order['unfinished_amount'] ?></td>
          <td><?= $order['finished_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计</td>
          <td><?= $return_freight?></td>
          <td><?= $return_make_from_price?></td>
          <td><?= $return_shipping_sale?></td>
          <td><?= $return_all_amount?></td>
          <td><?= $return_unfinished_amount?></td>
          <td><?= $return_finished_amount?></td>
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
            <td><?= $all_amount + $return_all_amount?></td>
            <td><?= $unfinished_amount + $return_unfinished_amount?></td>
            <td><?= $finished_amount + $return_finished_amount?></td>
          </tr>
        </table>
    </div>
</div>
