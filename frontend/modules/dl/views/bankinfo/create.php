<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Bankinfo */

$this->title = '填写银行卡信息';
$this->params['breadcrumbs'][] = ['label' => 'Bankinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;

?>
<div class="bankinfo-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
		'bankname' => $bankname,
		'source' => $source,
    ]) ?>

</div>
