<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\ShippingTpye;
use common\models\Area;
use common\models\User;
use common\models\GoodsInfo;
use common\models\LogisticsOrder;
use common\models\LogisticsRoute;
use backend\models\OrderRemark;
use common\models\OrderParts;
use frontend\assets\EmployeeEditAsset;
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
        <?= Html::button('打印收据', ['class'=>'btn btn-primary js-print-kd','data-order-id'=>$model->order_id])?>
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
          //  'order_sn',
            [
                'attribute' => 'order_type',
                'value' => function ($model) {
                    switch ($model->order_type) {
                        case 1:
                            return '西部';
                        case 3:
                            return '瑞胜';
                        case 4:
                            return '塔湾';
                        
                    }
                }
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
//             [
//                 'attribute' => 'receiving_areaid',
//                 'value' => function($model) {
//                     return Area::getAreaNameById($model->receiving_areaid);
//                 },
//             ],
            'receiving_name_area',
            [
                'label' => '线路',
                'attribute' => 'logistics_route_id',
                'value' => function($model){
                    return LogisticsRoute::findOne($model->logistics_route_id)->logistics_route_name;
                },
            ],
            [
            	'label'=>'落地点',
            	'attribute'=>'terminus.terminus_name',
            ],
            [
                'label' => '司机',
                'attribute' => 'driver_member_id',
                'value' => function($model){
                    if(empty($model->driver_member_id)){
                        return '';
                    }else{
                        return User::findOne($model->driver_member_id)->user_truename;
                    }
                },
            ],
			[
                'attribute' => 'order_id',
				'label' => '商品信息',
                'value' =>function($model) {
                    return GoodsInfo::getGoodsInfoById($model->order_id);
                },
            ],

            [
                'label' => '备注',
                'value' =>function($model) {
                    if(!empty(OrderRemark::findOne($model->order_id))){
                        return OrderRemark::findOne($model->order_id)->edit_content;
                    }
                },
            ],

            [
                'label' => '配件',
                'value' =>function($model) {
                    return OrderParts::getPartsName($model->order_id);
                },
            ]

            

//             'logistics_route_id',
        ],
    ]) ?>
    <?php if ($_SESSION['__id']==804):?>
    <?=DetailView::widget([
            'model' => $model,
            'attributes' => [

//           只有当管理员账号为dylan时，才能被查看显示的字段。
//            'freight_state',
                [
                    'attribute' => 'freight_state',
                    'value' => function($model) {
                        return $model -> getFreightStateName($model->freight_state);
                    },
                ],
//            'goods_price_state',
                [
                    'attribute' => 'goods_price_state',
                    'value' => function($model) {
                        return $model -> getGoodsPriceStateName($model->goods_price_state);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '物流价钱生成时间',
                    'value' => function($model) {
                        $a= \common\models\OrderTime::getPriceTimeById($model->order_id);
                        return LogisticsOrder::getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '分捡时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getSorterTimeById($model->order_id);
                        return LogisticsOrder::getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '装车时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getRuckTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '卸货时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getUnloadTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '签收时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getSignedForTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '落地点收款时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getCollectionTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '财务收运费时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getIncomeFreightTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '财务付运费时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getPayFreightTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '财务收货款时间',
                    'value' => function($model) {
                        $a = \common\models\OrderTime::getIncomePriceTimeById($model->order_id);
                        return LogisticsOrder:: getTableTimeValue($a);
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '申请提现时间',
                    'value' => function($model) {
//                        return date('Y-m-d H:i:s',LogisticsOrder :: getMoreTime($model->order_id)['add_time']);
                        $value = LogisticsOrder :: getMoreTime($model->order_id)['add_time'];
                        if ($value==0)
                        {
                            return'时间未设置';
                        }
                        else
                        {
                            return date('Y-m-d H:i:s',$value);
                        }
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '财务付货款时间',
                    'value' => function($model) {
//                        return date('Y-m-d H:i:s',LogisticsOrder :: getMoreTime($model->order_id)['pay_time']);
                        $value = LogisticsOrder :: getMoreTime($model->order_id)['pay_time'];
                        if ($value==0)
                        {
                            return'时间未设置';
                        }
                        else
                        {
                            return date('Y-m-d H:i:s',$value);
                        }
                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '扫码开始时间',
                    'value' => function($model) {
//                        return date('Y-m-d H:i:s',LogisticsOrder :: getMoreTime($model->order_id)['pay_time']);
                        if (empty(LogisticsOrder :: getThenTime($model->order_id)['0']))
                        {
                            return'时间未设置';
                        }
                        else
                        {
                            $value = LogisticsOrder :: getThenTime($model->order_id)['0']['update_time'];
                            return date('Y-m-d H:i:s',$value);
                        }

                    },
                ],
                [
                    'attribute' =>'order_id',
                    'label' => '扫码结束时间',
                    'value' => function($model) {
//                        return date('Y-m-d H:i:s',LogisticsOrder :: getMoreTime($model->order_id)['pay_time']);
                        if (empty(LogisticsOrder :: getThenTime($model->order_id)['1']))
                        {
                            $value = LogisticsOrder :: getThenTime($model->order_id)['0']['update_time'];
                            return date('Y-m-d H:i:s',$value);
                        }
                        else
                        {
                            $value = LogisticsOrder :: getThenTime($model->order_id)['1']['update_time'];
                            return date('Y-m-d H:i:s',$value);
                        }
                    },
                ],

//            添加结束
                    ],
            "template"=>"<tr><th style='width: 732px;'>{label}</th><td>{value}</td></tr>",
        ])?>
    <?php endif;?>
</div>
