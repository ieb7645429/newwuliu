<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Terminus;
use yii\widgets\LinkPager;
use backend\modules\dl\assets\TellerPayTerminusDetailsAsset;
use backend\modules\dl\assets\TellerReturnPayTerminusDetailsAsset;

TellerPayTerminusDetailsAsset::register($this);
TellerReturnPayTerminusDetailsAsset::register($this);

$this->title = '运费管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    
    <h3>发货运费结算</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>落地点</th>
            <th>票号</th>
            <th>开单时间</th>
            <th>落地时间</th>
            <th>收运费时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费付款方式</th>
            <th>运费状态</th>
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
          <td><?= Terminus::getNameById($order['terminus_id'])?></td>
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['income_freight_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td><?= $order['all_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="12"><?= $all_amount ?></td>
        </tr>
        <tr>
          <td colspan="13">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
          </td>
        </tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
    
    <h3>退（返）货运费结算</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>落地点</th>
            <th>票号</th>
            <th>开单时间</th>
            <th>入库时间</th>
            <th>收运费时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费付款方式</th>
            <th>运费状态</th>
            <th>应付</th>
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
          <td><?= $order['object_name'] ?></td>
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['income_freight_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="return_freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td><?= $order['all_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="12"><?= $return_all_amount?></td>
        </tr>
        <tr>
          <td colspan="13">
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
            <td>未付</td>
            <td>已付</td>
          </tr>
          <tr>
            <td><?= $total['all_amount'] + $returnTotal['all_amount'] ?></td>
            <td><?= $total['unfinished_amount'] + $returnTotal['unfinished_amount'] ?></td>
            <td><?= $total['finished_amount'] + $returnTotal['finished_amount'] ?></td>
          </tr>
        </table>
    </div>
</div>
