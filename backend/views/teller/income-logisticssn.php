<?php
use yii\helpers\Html;
use backend\assets\TellerIncomeLogisticssnAsset;

TellerIncomeLogisticssnAsset::register($this);

$this->title = '收款-票号';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
        <div class="table-responsive">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="500px;">票号</th>
                <th>应收</th>
                <th>实收</th>
              </tr>
            </thead>
            <tbody class="income_logistics_sn">
              <tr>
                <td>
                  <?php echo Html::input('text', '', '', ['class'=>'form-control logistics_sn', 'id'=>'logistics_sn', 'data-index'=>1]);?>
                  <div class="alert alert-danger" id="logistics_sn_message" role="alert"></div>
                  <?php echo Html::hiddenInput('order_id[]','' , ['class'=>'orderId']);?>
                </td>
                <td class="amount"></td>
                <td><?php echo Html::input('text', '', '', ['class'=>'form-control rel_amount', 'id'=>'rel_amount']);?></td>
              </tr>
              <tr class="all_amount_body">
                <td>收款对象：<?php echo Html::input('text', '', '', ['class'=>'form-control', 'id'=>'receiving']);?></td>
                <td>合计：<span id="all_amount">0</span></td>
                <td>实收合计：<span id="rel_all_amount">0</span></td>
              </tr>
              <tr>
                <td colspan="3">
                  <?= Html::button('全部确认收款' ,['class' => 'btn btn-danger','id' => 'all-confirm-collection']) ?>
                  <?= Html::button('全部垫付', ['class' => 'btn btn-warning','id' => 'all-confirm-collection2', 'data-advance'=>1]) ?>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
</div>
