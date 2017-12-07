<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\RetuenUpdateAsset;

RetuenUpdateAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '更新返货单: ' . $model->order_id;
$this->params['breadcrumbs'][] = ['label' => '退货单', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_id, 'url' => ['view', 'id' => $model->order_id]];
$this->params['breadcrumbs'][] = '更新';
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'orderRemark' => $orderRemark,
        'returnInfo' =>$returnInfo
    ]) ?>

    <?= Html::input('hidden', null, Url::toRoute(['member/receiving']), ['id' => 'receivingUrl'])?>
    <?= Html::input('hidden', null, Url::toRoute(['return/member-info']), ['id' => 'memberInfoUrl'])?>
</div>
