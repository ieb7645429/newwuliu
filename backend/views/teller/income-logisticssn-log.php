<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use backend\assets\TellerIncomeLogisticssnLogAsset;

TellerIncomeLogisticssnLogAsset::register($this);

$this->title = '票号收款列表';
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

<div class="teller-income-sn-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <div class="teller-income-sn-log-search" style="width:350px;">
    
        <?php $form = ActiveForm::begin([
            'action' => [Yii::$app->controller->action->id],
            'method' => 'get',
        ]); ?>
    
        <?= $form->field($searchModel, 'add_time')->label('打印时间')->widget(DateRangePicker::classname(), [
                'convertFormat'=>true,
                'presetDropdown'=>true,
                'model'=>$searchModel,
                'options' => [
                    'class' => 'form-control',
                    'value' => Yii::$app->request->get('ApplyForWithdrawalSearch')['add_time'] ? Yii::$app->request->get('ApplyForWithdrawalSearch')['add_time'] : '' ,
                ],
                'pluginOptions'=>[
                    'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
                ]
        ])?>
    
        <?php  echo $form->field($searchModel, 'number')->textInput(['id'=>'remark_number']); ?>
        
        <?php  echo $form->field($searchModel, 'receiving')->textInput(['list'=>'receivingList']); ?>
        <datalist id="receivingList">
            <?php if($receivingList):?>
            <?php foreach ($receivingList as $receiving):?>
            <option value="<?= $receiving['receiving']?>">
            <?php endforeach;?>
            <?php endif;?>
        </datalist>
        
        <?= $form->field($searchModel, 'goods_state')
             ->label('收款状态')
             ->dropDownList(['0' => '全部', '1'=>'已收', '2'=>'未收'], []);
        ?>
    
        <div class="form-group">
            <?= Html::hiddenInput('download_type', '0', ['id' => 'download_type']) ?>
            <?= Html::Button('查询', ['class' => 'btn btn-primary', 'id' => 'searchButton']) ?>
            <?php echo Html::Button('导出Excel', ['class' => 'btn btn-warning', 'id' => 'downloadExcel']); ?>
            <?php echo Html::Button('打印', ['class' => 'btn btn-success', 'id' => 'printButton']); ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'number',
            'order_sn',
            'amount',
            'rel_amount',
            'receiving',
            [
                'label' => '收款人',
                'attribute' => 'userTrueName',
                'value' => 'userTrueName.user_truename',
            ],
            [
                'attribute' => 'add_time',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            [
                'label' => '送货状态',
                'value' => function($model) {
                    return $model->getOrderState();
                }
            ],
            [
                'label' => '代收款状态',
                'value' => function($model) {
                    return $model->getGoodsPriceState();
                }
            ],
            [
                'label' => '备注',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getRemark();
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{collection} {collection2}',
                'buttons' => [
                    'collection' => function ($url, $model, $key) {
                        return Html::button('收款', ['id'=>'confirm-collection_'.$model->order_id,'class' => 'btn btn-danger confirm-collection','data-id'=>$model->order_id]);
                    },
                    'collection2' => function ($url, $model, $key) {
                        return Html::button('垫付', ['id'=>'confirm-collection_'.$model->order_id,'class' => 'btn btn-danger confirm-collection','data-id'=>$model->order_id, 'data-advance'=>1]);
                    },
                ],
                'visibleButtons' => [
                    'collection' => function ($model, $key, $index) {
                        return $model->getCollectionDisplay();
                    },
                    'collection2' => function ($model, $key, $index) {
                        return $model->getCollection2Display();
                    },
                ]
            ]
        ],
    ]); ?>
</div>
