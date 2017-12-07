<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel frontend\models\CustomerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '线路查询';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            [
                'label' => '网点名称',
                'attribute' => 'name',
            ],
            [
                'label' => '客户类型',
                'attribute' => 'customer_type',
                'contentOptions' => ['width' => '80px'],
            ],
//            [
//                'label' => '客户编号',
//                'attribute' => 'customer_num',
//            ],
            [
                'label' => '客户名称',
                'attribute' => 'customer_name',
            ],
            [
                'label' => '联系人',
                'attribute' => 'contact_person',
            ],
            [
                'label' => '座机',
                'attribute' => 'telephone',
            ],
            [
                'label' => '手机',
                'attribute' => 'mobilephone',
            ],
            [
                'label' => '地址',
                'attribute' => 'address',
            ],
//            [
//                'label' => '坐标',
//                'attribute' => 'coord',
//            ],
            [
                'label' => '创建时间',
                'attribute' => 'create_time',
            ],
            [
                'label' => '备注',
                'attribute' => 'remarks',
            ],
            [
                'label' => '所属线路',
                'attribute' => 'route',
            ],
//            [
//                'label' => '是否有商城账号',
//                'attribute' => 'maccount_having',
//            ],
//            [
//                'label' => '商城账号',
//                'attribute' => 'mall_account',
//            ],
//            [
//                'label' => '是否开通',
//                'attribute' => 'open_up',
//            ],
//            [
//                'label' => '是否采集',
//                'attribute' => 'collection',
//            ],
//            [
//                'label' => '是否旗舰',
//                'attribute' => 'ultimate',
//            ],

//            [
//                'class' => 'yii\grid\ActionColumn',
//                'template' => '',
//            ],

        ],
    ]); ?>
</div>
