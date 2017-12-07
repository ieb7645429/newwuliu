<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\buyinfo */

$this->title = 'Create Buyinfo';
$this->params['breadcrumbs'][] = ['label' => 'Buyinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="buyinfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
