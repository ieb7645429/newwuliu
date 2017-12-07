<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderEdit */

$this->title = 'Update Logistics Order Edit: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Logistics Order Edits', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistics-order-edit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
