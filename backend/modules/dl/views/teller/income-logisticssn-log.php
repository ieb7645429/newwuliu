<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\TellerIncomeLogisticssnLogAsset;

TellerIncomeLogisticssnLogAsset::register($this);

$this->title = '票号收款列表';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="teller-income-sn-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'order_sn',
            'count',
            'amount',
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
                'class' => 'yii\grid\ActionColumn',
                'template' => '{print}',
                'buttons' => [
                    'print' => function ($url, $model, $key) {
                        return Html::button('打印', ['id'=>'print_'.$key,'class' => 'btn btn-danger print','data-id'=>$key]);
                    },
                ]
            ]
        ],
    ]); ?>
</div>
