<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

use backend\assets\TellerAdvanceAsset;

TellerAdvanceAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderAdvanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '垫付单列表';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="order-advance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="order-advance-search">
    
        <?php $form = ActiveForm::begin([
            'action' => [Yii::$app->controller->action->id],
            'method' => 'get',
        ]); ?>
    
        <?= $form->field($searchModel, 'logistics_sn') ?>
    
        <div class="form-group">
            <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        </div>
    
        <?php ActiveForm::end(); ?>
    
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'logistics_sn',
            'amount',
            [
                'attribute' => 'state',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="state_name_' . $model->id . '">' . $model->getStateName() . '</span>';
                },
            ],
            [
                'attribute' => 'add_time',
                'format' => ['date', 'php:Y-m-d H:i'],
            ],
            // 'add_user',
            [
                'attribute' => 'income_time',
                'format' => 'html',
                'value' => function($model) {
                    return '<span class="income_time_' . $model->id . '">' . ($model->income_time?date('Y-m-d H:i', $model->income_time):'') . '</span>';
                },
            ],
            // 'income_user',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{confirm}',
                'buttons' => [
                    'confirm' => function ($url, $model, $key) {
                        return $model->state == '2' ? Html::button('确认收款', ['id'=>'confirm-collection_'.$key,'class' => 'btn btn-danger confirm-collection','data-id'=>$key]): '';
                    },
                ]
            ],
        ],
    ]); ?>
</div>
