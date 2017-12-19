<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\ReturnCreate3Asset;
ReturnCreate3Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '添加追回单';
$this->params['breadcrumbs'][] = ['label' => '追回单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-return-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form3', [
        'model' => $model,
        'orderRemark' => $orderRemark,
    ]) ?>

    <?= Html::input('hidden', null, Url::toRoute(['member/receiving']), ['id' => 'receivingUrl'])?>
    <?= Html::input('hidden', null, Url::toRoute(['return/member-info']), ['id' => 'memberInfoUrl'])?>
</div>
