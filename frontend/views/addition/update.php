<?php

use yii\helpers\Html;
use frontend\assets\AdditionAsset;
AdditionAsset::register($this);
$this->params['leftmenus'] = $menus;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsRoute */

$this->title = '线路修改:';
$this->params['breadcrumbs'][] = ['label' => 'Logistics Routes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->logistics_route_id, 'url' => ['view', 'id' => $model->logistics_route_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="logistics-route-update">

    <p>
    <?= Html::a('添加线路', ['add-route'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('查询所有线路', ['index'], ['class' => 'btn btn-success']); ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>


    <?= $this->render('_form2', [
        'model' => $model,
        'modelCar' => $modelCar,
        'driversRes' => $driversRes,
        'area' => $area,
        'model_driver'=> $model_driver,
        'logiscticAreaInfo' => $logiscticAreaInfo,
        'id' => $id,
    ]) ?>

</div>