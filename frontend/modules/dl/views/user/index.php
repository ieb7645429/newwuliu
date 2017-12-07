<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员号查询';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//             ['class' => 'yii\grid\SerialColumn'],

//             'id',
            'username',
            [
                'label' => '真实姓名',
                'attribute' => 'user_truename',
                'options' => ['style'=>'width:50%;'],
            ],
//             'auth_key',
//             'password_hash',
//             'password_reset_token',
            // 'email:email',
            // 'status',
            // 'created_at',
            // 'updated_at',
            // 'user_truename',
            // 'is_poundage',
            // 'is_buy_out',
            // 'buy_out_price',
            // 'buy_out_time:datetime',
            // 'member_phone',
            // 'member_areaid',
            // 'member_cityid',
            // 'member_provinceid',
            // 'member_areainfo',
            // 'App_Key',
            // 'small_name',

//             ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
