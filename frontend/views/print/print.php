<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\assets\PrintAsset;
PrintAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = '打印贴纸';
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

   <form>
    <label>打印数量</label>
     <input id="num" type="number" step="1" min="1" value="1" />
     <input type="button" value="打印"  onclick="javascript:print_tag(document.getElementById('num').value);"/>
   </form>
</div>
