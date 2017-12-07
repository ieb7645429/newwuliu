<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '财务';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_return-search', ['model' => $searchModel]); ?>

    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>退货员</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>应收</th>
            <th>未收</th>
            <th>已收</th>
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
          <td colspan="4">合计</td>
          <td><?= $all_amount?></td>
          <td><?= $unfinished_amount?></td>
          <td><?= $finished_amount?></td>
        </tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
</div>
