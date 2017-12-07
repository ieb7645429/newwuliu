<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\modules\hlj\assets\emberUpdateAsset;
MemberUpdateAsset::register($this);
/* @var $this yii\web\View */
/* @var $model common\models\LogisticsOrder */

$this->title = '修改发货单';
$this->params['breadcrumbs'][] = ['label' => '发货单', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->order_id, 'url' => ['view', 'id' => $model->order_id]];
$this->params['breadcrumbs'][] = '更新';
$this->params['leftmenus'] = $menus;
?>
<div class="logistics-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_memberform', [
        'model' => $model,
        'area' => $area,
    	'user' => $user,
    ]) ?>
<?= Html::input('hidden', null, Url::toRoute(['member/receiving']), ['id' => 'receivingUrl'])?>
</div>
