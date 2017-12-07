<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\OrderThirdAdvancesearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '冻结订单';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="order-third-advance-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<p>
        冻结金额:<?= $sumAmount?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'id',
//             'order_id',
//             'member_id',
                'logistics_sn',
            'amount',
            
            // 'state',
            // 'add_time',
            // 'add_user',
            // 'income_time',
            // 'income_user',

//             ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
