<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use frontend\controllers\LogisticsOrderDeliveController;

/* @var $this yii\web\View */
/* @var $searchModel common\models\LogisticsOrderDeliveSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '司机配送信息明细';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<style>
.form-group{
    width:50%;
}
</style>
<div class="logistics-order-index1">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//         'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],
            'logistics_sn',
            'freight',
            'goods_price',
            'goods_num',
            'member_name',
            'member_phone',
            'receiving_name',
            'receiving_phone',
//             ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
