<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Bankinfo */

$this->title = 'Update Bankinfo: ' . $model->bank_info_id;
$this->params['breadcrumbs'][] = ['label' => 'Bankinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->bank_info_id, 'url' => ['view', 'id' => $model->bank_info_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bankinfo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
