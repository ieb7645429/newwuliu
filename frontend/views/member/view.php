<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\Area;
use common\models\ShippingTpye;
use common\models\GoodsInfo;
/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = '货单详情';
$this->params['breadcrumbs'][] = ['label' => '查看', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <!--<?= Html::a('修改', ['update', 'id' => $model->order_id], ['class' => 'btn btn-primary']) ?>-->
        <!--<?= Html::a('Delete', ['delete', 'id' => $model->order_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>-->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//             'order_id',
            'logistics_sn',
//             [
//                 'attribute' => 'order_sn',
//                 'visible' => ($model->order_type != 1)
//             ],
            [
                'attribute' => 'order_sn',
                'value' => function ($model) {
                if(!empty($model->order_sn)&&!is_numeric($model->order_sn)){
                    return unserialize($model->order_sn);
                }
                return $model->order_sn;
                }
            ],
            [
                'attribute' => 'freight',
                'visible' => ($model->order_state > 5)
            ],
            'goods_price',
            [
                'attribute' => 'make_from_price',
                'visible' => ($model->order_state > 5)
            ],
            [
                'attribute' => 'goods_num',
                'visible' => ($model->order_state > 5)
            ],
            [
                'attribute' => 'order_state',
                'value' => function ($model) {
                    return $model -> getOrderStateName($model->order_state,$model);
                }
            ],
//             'state',
//             'abnormal',
            [
                'attribute' => 'shipping_type',
                'value' => function($model) {
                    return ShippingTpye::getShippingTypeNameById($model->shipping_type);
                }
            ],
            [
                'attribute' => 'collection',
                'value' => function($model) {
                    return $model -> getCollectionName($model->collection);
                }
            ],
            [
                'attribute' => 'collection_poundage_one',
                'visible' => ($model->order_state > 5)
            ],
            [
                'attribute' => 'collection_poundage_two',
                'visible' => ($model->order_state > 5)
            ],
//             'order_type',
//             'add_time',
            'member_name',
//             'member_id',
            [
                'attribute' => 'member_cityid',
                'value' => function($model) {
                    return Area::getAreaNameById($model->member_cityid);
                },
            ],
            'member_phone',
            'receiving_name',
            'receiving_phone',
            [
                'attribute' => 'receiving_cityid',
                'value' => function($model) {
                    return Area::getAreaNameById($model->receiving_cityid);
                },
            ],
            [
                'attribute' => 'receiving_areaid',
                'value' => function($model) {
                    return Area::getAreaNameById($model->receiving_areaid);
                },
            ],
            'receiving_name_area',
//             'terminus_id',
//             'logistics_route_id',
				[
					'attribute' => 'order_id',
					'label' => '商品信息',
					'value' =>function($model) {
						return GoodsInfo::getGoodsInfoById($model->order_id);
					},
				],
            ],

		
    ]) ?>

</div>
