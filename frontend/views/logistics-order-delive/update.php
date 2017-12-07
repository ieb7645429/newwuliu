<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = 'Update Logistics Order: ' . $model->order_id;
$this->params['breadcrumbs'][] = ['label' => 'Logistics Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_id, 'url' => ['view', 'id' => $model->order_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistics-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
