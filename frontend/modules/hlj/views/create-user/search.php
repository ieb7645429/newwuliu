<?php

use yii\helpers\Html;
use frontend\assets\CreateUserSearchAsset;

CreateUserSearchAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '查询会员';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="form-group">
        <label for="phone">电话</label>
        <?= Html::input('text', 'phone', '', ['id'=>'phone', 'class'=>"form-control"])?>
    </div>
    <div class="form-group">
        <label for="store_name">店铺名</label>
        <?= Html::input('text', 'store_name', '', ['id'=>'store_name', 'class'=>"form-control"])?>
    </div>
     <div class="form-group">
        <label for="store_name">小号名称</label>
        <?= Html::input('text', 'small_num', '', ['id'=>'small_num', 'class'=>"form-control"])?>
    </div>
    <div class="form-group">
    <?= Html::button('查询', ['class' => 'btn btn-primary', 'id'=>'searchButton'])?>
    </div>
    <div id="result"></div>
</div>
