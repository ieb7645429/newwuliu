<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrderEdit */

$this->title = 'Create Logistics Order Edit';
$this->params['breadcrumbs'][] = ['label' => 'Logistics Order Edits', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistics-order-edit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
