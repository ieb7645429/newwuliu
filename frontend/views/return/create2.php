<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\ReturnCreateTwoAsset;
ReturnCreateTwoAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '添加退货单';
$this->params['breadcrumbs'][] = ['label' => '退货单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form2', [
        'model' => $model,
        'orderRemark' => $orderRemark,
    ]) ?>
    <?= Html::input('hidden', null, Url::toRoute(['member/receiving']), ['id' => 'receivingUrl'])?>
    <?= Html::input('hidden', null, Url::toRoute(['return/member-info']), ['id' => 'memberInfoUrl'])?>
</div>
