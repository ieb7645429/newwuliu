<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\modules\hlj\assets\EmployeeCreateAsset;
use components\autocomplete\AutoCompltetAsset;
EmployeeCreateAsset::register($this);
AutoCompltetAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = '添加发货单';
$this->params['breadcrumbs'][] = ['label' => '货单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_form', [
        'model' => $model,
        'area' => $area,
        'user' => $user,
        'logisticsRouteInfo' => $logisticsRouteInfo,
        'areaName' => $areaName,
        'disabled' => false,
        'orderRemark' => $orderRemark,
    ]) ?>

    <?= Html::input('hidden', null, Url::toRoute(['member/receiving']), ['id' => 'receivingUrl'])?>
    <?= Html::input('hidden', null, Url::toRoute(['employee/member-info']), ['id' => 'memberInfoUrl'])?>
</div>
