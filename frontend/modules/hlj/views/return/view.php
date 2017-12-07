<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use frontend\modules\hlj\models\Area;
use frontend\modules\hlj\models\ShippingTpye;
use frontend\modules\hlj\assets\ReturnViewAsset;
use frontend\modules\hlj\models\ReturnOrderRemark;
use frontend\modules\hlj\models\ReturnInfo;
ReturnViewAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '货单详情';
$this->params['breadcrumbs'][] = ['label' => 'Logistics Return Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<input type="hidden" id="tag_print" value="<?=$print?>">
<input type="hidden" id="tag_order_id" value="<?=$model->order_id?>">
<div class="logistics-return-order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::button('打印', ['class'=>'btn btn-primary js-print','data-order-id'=>$model->order_id])?>
        <?php /*echo Html::a('Delete', ['delete', 'id' => $model->order_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//             'order_id',
            'logistics_sn',
            'ship_logistics_sn',
            'goods_sn',
//             'order_sn',
            [
                'attribute' => 'shipping_type',
                'value' => function($model) {
                    return ShippingTpye::getShippingTypeNameById($model->shipping_type);
                }
            ],
            'freight',
            'goods_price',
            'make_from_price',
            'goods_num',
            [
                'attribute' => 'order_state',
                'value' => function ($model) {
                    return $model -> getOrderStateName($model->order_state);
                }
            ],
//             'state',
//             'abnormal',
            [
                'attribute' => 'collection',
                'value' => function($model) {
                    return $model -> getCollectionName($model->collection);
                }
            ],
            [
                'attribute' => 'collection_poundage_two',
                'visible' => ($model->collection == 1)
            ],
//             'order_type',
            [
                'attribute' => 'return_type',
                'value' => function($model) {
                    return $model -> getReturnTypeName($model->return_type);
                }
            ],
            'member_phone',
            'member_name',
//             'member_id',
            [
                'attribute' => 'member_cityid',
                'value' => function($model) {
                    return Area::getAreaNameById($model->member_cityid);
                },
            ],
            'receiving_phone',
            'receiving_name',
            [
                'attribute' => 'receiving_cityid',
                'value' => function($model) {
                    return Area::getAreaNameById($model->receiving_cityid);
                },
            ],
//             [
//                 'attribute' => 'receiving_areaid',
//                 'value' => function($model) {
//                     return Area::getAreaNameById($model->receiving_areaid);
//                 },
//             ],
//             'receiving_name_area',
//             'terminus_id',
//             'logistics_route_id',
            [
                'attribute' => 'add_time',
                'format' => ['date', 'php:Y-m-d H:i:s']
            ],
			[
                'attribute' => 'order_id',
				'label' => '商品信息',
                'value' =>function($model) {
                    return ReturnInfo::getGoodsInfoById($model->order_id);
                },
            ],
            [
                'label' => '备注',
                'value' =>function($model) {
                $content = ReturnOrderRemark::findOne($model->order_id);
                if(!empty($content)) {
                    return $content->edit_content;
                }
                },
            ],
        ],
    ]) ?>

    <?php if($model->return_type == 1 && $model->return_all == 2):?>
    <h3>返货商品详情</h3>
    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'name',
                'number',
                'price',
            ],
        ]);
    ?>
    <?php endif;?>
</div>
