<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Terminus;
use yii\widgets\LinkPager;
use yii\bootstrap\Modal;
use backend\assets\TellerPayTerminusDetailsAsset;
use backend\assets\TellerReturnPayTerminusDetailsAsset;

TellerPayTerminusDetailsAsset::register($this);
TellerReturnPayTerminusDetailsAsset::register($this);

$this->title = '财务';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>

<?php
Modal::begin([
    'id' => 'remark-modal',
    'header' => '<h4 class="modal-title">订单备注</h4>',
]);?>
<div id="remark-modal-body">

</div>
<?php Modal::end();?>

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
    <h3>发货运费结算</h3>
    <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th><?= Html::checkbox('check_all', false, ['id' => 'check_all']) ?></th>
            <th>落地点</th>
            <th>票号</th>
            <th>开单时间</th>
            <th>落地时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费付款方式</th>
            <th>运费状态</th>
            <th width="150">备注</th>
            <th>应付</th>
            <td></td>
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
          <td><?= Html::checkbox('order_id', false, ['class'=>'order_check order_check_'.$order['order_id'], 'value' => $order['order_id'],'disabled'=>$order['is_confirm']]) ?></td>
          <td><?= Terminus::getNameById($order['terminus_id'])?></td>
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="remark"><?= Html::button('查看修改备注', ['id'=>'remark_'.$order['order_id'],'class' => 'btn btn-info remark_edit','data-id'=>$order['order_id'],'data-toggle' => 'modal','data-target' => '#remark-modal'])?></td>
          <td><?= $order['all_amount'] ?></td>
          <td>
            <?php if(!$order['is_confirm']):?>
            <?= Html::button('确认付款', ['id'=>'confirm-collection_'.$order['order_id'],'class' => 'btn btn-danger confirm-collection','data-order-id'=>$order['order_id'],'disabled'=>$order['is_confirm']]) ?>
            <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="11"><?= $all_amount ?></td>
        </tr>
        <tr><td colspan="12"><?= Html::button('全部确认付款', ['class' => 'btn btn-danger','id' => 'all-confirm-collection']) ?></td></tr>
        <tr>
          <td colspan="12">
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
            <th><?= Html::checkbox('return_check_all', false, ['id' => 'return_check_all']) ?></th>
            <th>落地点</th>
            <th>票号</th>
            <th>开单时间</th>
            <th>入库时间</th>
            <th>运费</th>
            <th>制单费</th>
            <th>运费付款方式</th>
            <th>运费状态</th>
            <th width="150">备注</th>
            <th>应付</th>
            <th></th>
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
          <td><?= Html::checkbox('return_order_id', false, ['class'=>'return_order_check return_order_check_'.$order['order_id'],'value' => $order['order_id'],'disabled'=>$order['is_confirm']]) ?></td>
          <td><?= $order['object_name'] ?></td>
          <td><?= $order['logistics_sn'] ?></td>
          <td><?= $order['price_time'] ?></td>
          <td><?= $order['unload_time'] ?></td>
          <td><?= $order['freight'] ?></td>
          <td><?= $order['make_from_price'] ?></td>
          <td><?= $order['shipping_type_name'] ?></td>
          <td class="return_freight_state_name_<?= $order['order_id']?>"><?= $order['freight_state_name'] ?></td>
          <td class="return_remark" ><?= Html::button('查看修改备注', ['id'=>'remark_'.$order['order_id'],'class' => 'btn btn-info return_remark_edit','data-id'=>$order['order_id'],'data-toggle' => 'modal','data-target' => '#remark-modal'])?></td>
          <td><?= $order['all_amount'] ?></td>
          <td>
            <?php if(!$order['is_confirm']):?>
            <?= Html::button('确认付款', ['id'=>'return_confirm-collection_'.$order['order_id'],'class' => 'btn btn-danger return_confirm-collection','data-order-id'=>$order['order_id'],'disabled'=>$order['is_confirm']]) ?>
            <?php endif;?>
          </td>
        </tr>
        <?php endforeach;?>
        <tr>
          <td>合计：
          </td>
          <td colspan="11"><?= $return_all_amount?></td>
        </tr>
        <tr><td colspan="12"><?= Html::button('全部确认付款', ['class' => 'btn btn-danger','id' => 'return_all-confirm-collection']) ?></td></tr>
        <tr>
          <td colspan="12">
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
