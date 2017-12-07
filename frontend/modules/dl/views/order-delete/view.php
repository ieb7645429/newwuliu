<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ShippingTpye;
use common\models\Area;
use common\models\GoodsInfo;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderDelete */

$this->title = '货单详情';
$this->params['breadcrumbs'][] = ['label' => 'Logistics Order Deletes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-delete-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//             'order_id',
            'logistics_sn',
          //  'order_sn',
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
                'label' => '会员号',
                'attribute' => 'userName',
                'value' => function ($model) {
                return $model -> idToUserName($model->member_id);
            }
            ],
            'freight',
            'goods_price',
            'make_from_price',
            'goods_num',
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
            'collection_poundage_one',
            'collection_poundage_two',
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
//             [
//             	'label'=>'落地点',
//             	'attribute'=>'terminus.terminus_name',
//             ],
			[
                'attribute' => 'order_id',
				'label' => '商品信息',
                'value' =>function($model) {
                    return GoodsInfo::getGoodsInfoById($model->order_id);
                },
            ],

//             'logistics_route_id',
        ],
    ]) ?>

</div>
