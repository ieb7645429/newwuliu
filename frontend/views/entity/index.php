<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */

$this->title = '鹰眼离线用户';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="list-entity">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['label'=>'用户名','attribute'=>'username','value'=>function($model){
                return  $model['username'];
            }],
            ['label'=>'姓名','attribute'=>'user_truename'],
            ['label'=>'电话','attribute'=>'member_phone'],
            ['label'=>'地址','attribute'=>'address'],
            ['label'=>'时间','attribute'=>'loc_time'],
            ['label'=>'状态','value'=>function($model){
                return '离线（10分钟内无定位点）';
            }]
        ],
        'layout' => "\n{items}\n{pager}",
    ]); ?>
</div>
