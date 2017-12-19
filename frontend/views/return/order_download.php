<?php
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = '退货单导出Excel';
$this->params['breadcrumbs'][] = $this->title;

$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php foreach ($datas as $a):?>
    <?= Html::a($a['content'], Url::to($a['url'], true)) ?>
    <?php endforeach;?>
</div>
