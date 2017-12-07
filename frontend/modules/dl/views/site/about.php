<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use frontend\assets\SiteAboutAsset;
use components\Lodop\LodopAsset;

SiteAboutAsset::register($this);
LodopAsset::register($this);

$this->title = 'About';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>This is the About page. You may modify the following file to customize its content:</p>
    <?= Html::input('button','','打印', ['id'=>'print-test', 'class' => 'btn btn-primary', 'name' => 'signup-button']) ?>

    <code><?= __FILE__ ?></code>
</div>
