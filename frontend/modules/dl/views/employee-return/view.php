<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ShippingTpye;
use common\models\Area;
use common\models\GoodsInfo;
use frontend\modules\dl\assets\EmployeeEditAsset;
EmployeeEditAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = '货单详情';
$this->params['breadcrumbs'][] = ['label' => '查看', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if($role != '落地点'){
    $this->params['leftmenus'] = $menus;
}
?>
<input type="hidden" id="tag_print" value="<?=$print?>">
<input type="hidden" id="tag_order_id" value="<?=$model->order_id?>">
<div class="logistics-order-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //Html::a('修改', ['update', 'id' => $model->order_id], ['class' => 'btn btn-primary']) ?>
        <?php if($role != '落地点'){?>
        <?= Html::button('打印', ['class'=>'btn btn-primary js-print','data-order-id'=>$model->order_id])?>
        <?php }?>
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
            [
                    'attribute' => 'return_logistics_sn',
                    'label' => '返货单编号'
            ],
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
                    return $model -> getOrderStateName($model->order_state);
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
            [
            	'label'=>'落地点',
            	'attribute'=>'terminus.terminus_name',
                ],
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
