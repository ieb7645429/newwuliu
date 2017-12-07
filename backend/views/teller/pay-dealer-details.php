<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\assets\TellerPayDealerDetailsAsset;

TellerPayDealerDetailsAsset::register($this);
$this->title = '财务';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php if($bankModel !== null):?>
    <?= DetailView::widget([
        'model' => $bankModel,
        'attributes' => [
            'bank_info_card_no',
            'bank_info_account_name',
            'bank_info_bank_name',
        ],
    ]) ?>
    <?php endif;?>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th><?= Html::checkbox('check_all', false, ['id' => 'check_all']) ?></th>
            <th>经销商</th>
            <th>票号</th>
            <th>会员费</th>
            <th>代收手续费</th>
            <th>运费</th>
            <th>制单费</th>
            <th>佣金</th>
            <th>代收款</th>
            <th>返货货值</th>
            <th>运费优惠价钱</th>
            <th>买断状态</th>
            <th>运费付款方式</th>
            <th>运费状态</th>
            <th>代收款状态</th>
            <th>应付</th>
            <th></th>
          </tr>
        </thead>
        <?php if ($orderList):?>
        <tbody>
        <?php foreach ($orderList as $key => $order):?>
        <tr>
          <td><?= Html::checkbox('order_id', false, ['class'=>'order_check order_check_'.$order['order_id'],'value' => $order['order_id'],'disabled'=>$order['is_confirm']]) ?></td>
          <td><?= $order['member_name'] ?></td>
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['collection_poundage_one'] ?></td>
          <td><?= $order['collection_poundage_two'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['goods_price_scale'] ?></td>
          <td><?= $order['goods_price'] ?></td>
          <td><?= $order['return_goods_price'] ?></td>
          <td><?= $order['shipping_sale'] ?></td>
          <td><?= $order['state_name'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td><?= $order['freight_state_name'] ?></td>
          <td class="goods_price_state_name_<?= $order['order_id']?>"><?= $order['goods_price_state_name'] ?></td>
          <td><?= $order['all_amount'] ?></td>
          <td>
            <?php if(!$order['is_confirm']):?>
            <?= Html::button('确认付款', ['id'=>'confirm-collection_'.$order['order_id'],'class' => 'btn btn-danger confirm-collection','data-order-id'=>$order['order_id'],'disabled'=>$order['is_confirm']]) ?>
            <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
        <tr><td colspan="17"><?= Html::button('全部确认付款', ['class' => 'btn btn-danger','id' => 'all-confirm-collection']) ?></td></tr>
        </tbody>
        <?php endif;?>
      </table>
    </div>
</div>
