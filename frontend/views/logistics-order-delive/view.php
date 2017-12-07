<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = $model->order_id;
$this->params['breadcrumbs'][] = ['label' => 'Logistics Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistics-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->order_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->order_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'order_id',
            'logistics_sn',
            'goods_sn',
            'order_sn',
            'freight',
            'goods_price',
            'make_from_price',
            'goods_num',
            'order_state',
            'state',
            'freight_state',
            'goods_price_state',
            'abnormal',
            'collection',
            'collection_poundage_one',
            'collection_poundage_two',
            'order_type',
            'add_time',
            'member_name',
            'member_id',
            'member_cityid',
            'member_phone',
            'receiving_name',
            'receiving_phone',
            'receiving_name_area',
            'receiving_provinceid',
            'receiving_cityid',
            'receiving_areaid',
            'terminus_id',
            'logistics_route_id',
            'shipping_type',
            'employee_id',
            'driver_member_id',
            'test',
            'shipping_sale',
            'scale',
            'same_city',
            'return_logistics_sn',
            'buy_confirm',
        ],
    ]) ?>

</div>
