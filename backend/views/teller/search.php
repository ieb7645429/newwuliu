<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use common\models\ShippingTpye;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use backend\assets\TellerSearchAsset;

TellerSearchAsset::register($this);
/* @var $this yii\web\View */
/* @var $searchModel backend\models\TellerLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '订单查询';
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

<div class="teller-search">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="logistics-order-search">

    <?php $form = ActiveForm::begin([
        'action' => [Yii::$app->controller->action->id],
        'method' => 'get',
    ]); ?>
    <div style="width:350px;">
    <?= $form->field($searchModel, 'logistics_sn')->input('text') ?>
    
    <?= $form->field($searchModel, 'add_time')->label('查询时间')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$searchModel,
            'options' => [
                'class' => 'form-control',
                'value' => Yii::$app->request->get('TellerSearch')['add_time'] ? Yii::$app->request->get('TellerSearch')['add_time'] : date('Y-m-d') . ' - ' . date('Y-m-d'),
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
    ])?>
    
    <?= $form->field($searchModel, 'collection')
             ->label('代收状态')
             ->dropDownList(['0'=>'全部', '1'=>'代收','2'=>'不代收'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerSearch'), 'collection', '0')]);
    ?>
    
    <?= $form->field($searchModel, 'driverTrueName')
             ->label('司机名称')
             ->textInput();
    ?>
    
    <?= $form->field($searchModel, 'goods_price_state')
             ->label('收款类型')
             ->dropDownList(['0' => '全部', '1'=>'已收', '2'=>'未收'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerSearch'), 'goods_price_state', '0')]);
    ?>
    
    <?= $form->field($searchModel, 'order_state')
             ->label('订单状态')
             ->dropDownList(['0'=>'全部', '1'=>'已封车','2'=>'未封车'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerSearch'), 'order_state', '0')]);
    ?>
    
    <?= $form->field($searchModel, 'advance')
             ->label('垫付')
             ->dropDownList(['0'=>'全部', '1'=>'已追回','2'=>'已垫付'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerSearch'), 'advance', '0')]);
    ?>
    </div>
    
    <div class="form-group">
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

	</div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'logistics_sn',
                'format' => 'html',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getLogisticsSnLink();
                 },
            ],
            [
                'attribute' => 'order_state',
                'value' => function($model, $key, $index, $column) {
                    return $model -> getOrderStateName($model->order_state,$model);
                },
            ],
            'freight',
            'make_from_price',
            'goods_price',
            [
                'label' => '返货货值',
                'value' => function($model) {
                    return $model->getReturnGoodPrice();
                }
            ],
            'collection_poundage_one',
            'collection_poundage_two',
            [
                'label' => '运费优惠',
                'attribute' => 'shipping_sale'
            ],
            [
                'label' => '运费方式',
                'value' => function($model) {
                    return ShippingTpye::getShippingTypeNameById($model->shipping_type);
                }
            ],
            [
                'label' => '代收',
                'value' => function($model) {
                    return $model->getCollectionName($model->collection);
                }
            ],
            [
                'label' => '运费状态',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="freight_state_name_'.$model->order_id.'">'.$model->getFreightStateName($model->freight_state)."</span>";
                }
            ],
            [
                'label' => '代收款状态',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="goods_price_state_name_'.$model->order_id.'">'.$model->getGoodsPriceStateName($model->goods_price_state)."</span>";
                }
            ],
            [
                'label' => '垫付',
                'value' => function($model){
                    return $model->getAdvanceShow($model->order_id);
                },
            ],
            [
                'label' => '垫付时间',
                'value' => function($model) {
                    return $model->getAdvanceAddTime();
                },
            ],
            [
                'label' => '垫付人',
                'value' => function($model) {
                    return $model->getAdvanceAddUser();
                },
            ],
            [
                'label' => '收款时间',
                'value' => function($model) {
                    return $model->getAdvanceIncomeTime();
                },
            ],
            [
                'label' => '收款人',
                'value' => function($model) {
                    return $model->getAdvanceIncomeUser();
                },
            ],
            [
                'label' => '已付运费收款对象',
                'value' => function($model) {
                    return $model->getFreightMember();
                }
            ],
            [
                'label' => '提付运费和代收款收款对象',
                'value' => function($model) {
                    return $model->getGoodsPriceMember();
                }
            ],
            [
                'label' => '已付运费应收',
                'value' => function($model) {
                    return $model->getFreightValue();
                }
            ],
            [
                'label' => '提付运费和代收款应收',
                'value' => function($model) {
                    return $model->getGoodsPriceValue();
                }
            ],
            [
                'label' => '备注',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getRemark();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{freight} {goods} {goods_a} {freight_goods} {freight_goods_a}',
                'buttons' => [
                    'freight' => function ($url, $model, $key) {
                        return Html::button('运费收款', ['id'=>'confirm-freight_'.$key,'class' => 'btn btn-danger confirm-collection','data-url'=>$model->getFreightUrl(),'data-order-id'=>$key]);
                    },
                    'goods' => function ($url, $model, $key) {
                        return Html::button('代收款收款', ['id'=>'confirm-freight_goods_'.$key,'class' => 'btn btn-danger confirm-collection','data-url'=>$model->getGoodsFreightUrl(),'data-order-id'=>$key]);
                    },
                    'goods_a' => function ($url, $model, $key) {
                        return Html::button('代收款垫付', ['id'=>'confirm-freight_goods2_'.$key,'class' => 'btn btn-warning confirm-collection','data-url'=>$model->getGoodsFreightUrl(),'data-advance'=>1,'data-order-id'=>$key]);
                    },
                    'freight_goods' => function ($url, $model, $key) {
                        return Html::button('运费和代收款收款', ['id'=>'confirm-freight_goods_'.$key,'class' => 'btn btn-danger confirm-collection','data-url'=>$model->getGoodsFreightUrl(),'data-order-id'=>$key]);
                    },
                    'freight_goods_a' => function ($url, $model, $key) {
                        return Html::button('垫付', ['id'=>'confirm-freight_goods2_'.$key,'class' => 'btn btn-warning confirm-collection','data-url'=>$model->getGoodsFreightUrl(),'data-advance'=>1,'data-order-id'=>$key]);
                    },
                ],
                'visibleButtons' => [
                    'freight' => function ($model, $key, $index) {
                        return $model->getFreightDisplay();
                    },
                    'goods' => function ($model, $key, $index) {
                        return $model->getGoodsDisplay();
                    },
                    'goods_a' => function ($model, $key, $index) {
                        return $model->getGoodsDisplay();
                    },
                    'freight_goods' => function ($model, $key, $index) {
                        return $model->getFreightGoodsDisplay();
                    },
                    'freight_goods_a' => function ($model, $key, $index) {
                        return $model->getFreightGoodsDisplay();
                    }
                ]
            ],
        ],
    ]); ?>
</div>
