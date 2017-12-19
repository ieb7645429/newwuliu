<?php
/*************版本一*************/
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\AdditionAddAsset;
AdditionAddAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '添加线路';
$this->params['breadcrumbs'][] = ['label' => '添加线路', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;

?>
<p><?= Html::a('查询所有线路', ['index'], ['class' => 'btn btn-success']); ?></p>
<!--<div class="logistics-return-order-create">-->
<div class="addition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_addone', [
        'model' => $model,
        'modelLogisticsArea' => $modelLogisticsArea,
        'area' => $area,
        'menus' => $menus,
    ]) ?>
</div>
