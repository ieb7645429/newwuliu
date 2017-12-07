<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use common\models\ShippingTpye;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use backend\assets\TellerThirdAdvanceAsset;

TellerThirdAdvanceAsset::register($this);
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
                'value' => Yii::$app->request->get('TellerThirdAdvance')['add_time'] ? Yii::$app->request->get('TellerThirdAdvance')['add_time'] : '' ,
            ],
            'pluginOptions'=>[
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
    ])?>
    
    <?= $form->field($searchModel, 'goods_price_state')
             ->label('收款类型')
             ->dropDownList(['0' => '全部', '1'=>'已收', '2'=>'未收'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerThirdAdvance'), 'goods_price_state', '0')]);
    ?>
    
    <?= $form->field($searchModel, 'order_state')
             ->label('订单状态')
             ->dropDownList(['0'=>'全部', '1'=>'已封车','2'=>'未封车'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerThirdAdvance'), 'order_state', '0')]);
    ?>
    
    <?= $form->field($searchModel, 'advance')
             ->label('三方垫付')
             ->dropDownList(['0'=>'全部', '3'=>'未垫付','1'=>'已垫付','2'=>'已收款'],
                        ['value' => ArrayHelper::getValue(Yii::$app->request->get('TellerThirdAdvance'), 'advance', '0')]);
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
            'logistics_sn',
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
                'label' => '代收款状态',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="goods_price_state_name_'.$model->order_id.'">'.$model->getGoodsPriceStateName($model->goods_price_state)."</span>";
                }
            ],
            [
                'label' => '三方垫付',
                'contentOptions' => ['class' => 'advance_state'],
                'value' => function($model){
                    return $model->getAdvanceShow($model->order_id);
                },
            ],
            [
                'label' => '垫付时间',
                'contentOptions' => ['class' => 'advance_time'],
                'value' => function($model) {
                    return $model->getAdvanceAddTime();
                },
            ],
            [
                'label' => '垫付人',
                'contentOptions' => ['class' => 'advance_user'],
                'value' => function($model) {
                    return $model->getAdvanceAddUser();
                },
            ],
            [
                'label' => '应收',
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
                'template' => '{advance} {goods}',
                'buttons' => [
                    'advance' => function ($url, $model, $key) {
                        return Html::button('垫付', ['id'=>'confirm-advance_'.$key,'class' => 'btn btn-warning confirm-advance','data-order-id'=>$key]);
                    },
                    'goods' => function ($url, $model, $key) {
                        return Html::button('收款', ['id'=>'confirm-goods_'.$key,'class' => 'btn btn-danger confirm-collection','data-url'=>$model->getGoodsFreightUrl(),'data-order-id'=>$key]);
                    },
                ],
                'visibleButtons' => [
                    'advance' => function ($model, $key, $index) {
                        return $model->getAdvanceDisplay();
                    },
                    'goods' => function ($model, $key, $index) {
                        return $model->getGoodsDisplay();
                    },
                ]
            ],
        ],
    ]); ?>
</div>
