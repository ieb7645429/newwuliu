<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;

$this->title = '运费管理';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search_pay', ['model' => $searchModel]); ?>
    
    <h3>发货运费</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>开单员</th>
            <th>票号</th>
            <th>已付运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
            <th>运费状态</th>
            <th>应付</th>
          </tr>
        </thead>
        <?php 
            $all_amount = 0;
            $finished_amount = 0;
            $unfinished_amount = 0;
        ?>
        <?php if ($orderList):
		?>
        <tbody>
        <?php foreach ($orderList as $key => $order):
            $disabled = '';
            $arr = array('value' => $order['order_id'],'class'=>'order_check order_check_'.$order['order_id'],'data-id' => ($order['freight_state'] & 1)?'':'0');
            $btn= array('id'=>'confirm-collection_'.$order['order_id'], 'class' => 'btn btn-danger confirm-collection ','data-order-Id'=>$order['order_id']);
            if($order['freight_state'] & 1){
                $disabled =array("disabled"=>"disabled");
                $btn = array_merge($btn,$disabled);
            } 
        ?>
        <?php 
            $all_amount += $order['all_amount'];
            $finished_amount += $order['finished_amount'];
            $unfinished_amount += $order['unfinished_amount'];
        ?>
        <tr>
          <td><?= $order['object_name'] ?></td>
          <td  class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td class="freight_state_name_<?=$order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="8"><?= $all_amount?></td>
        </tr>
        <tr>
          <td colspan="9">
            <?= LinkPager::widget(['pagination' => $pages]); ?>
          </td>
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
            <th>物流单号</th>
            <th>已付运费</th>
            <th>制单费</th>
            <th>运费优惠价钱</th>
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
          <td class="sn" data-id="<?= $order['logistics_sn'] ?>"><?= $order['logistics_sn'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td class="return_freight_state_name_<?=$order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="amount" data-id="<?= $order['all_amount'] ?>"><?= $order['all_amount'] ?></td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="8"><?= $return_all_amount?></td>
        </tr>
        <tr>
          <td colspan="9">
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
          </tr>
          <tr>
            <td><?= $total['all_amount'] + $returnTotal['all_amount'] ?></td>
          </tr>
        </table>
    </div>
</div>
