<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = 'Create Logistics Order';
$this->params['breadcrumbs'][] = ['label' => 'Logistics Orders', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="logistics-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
