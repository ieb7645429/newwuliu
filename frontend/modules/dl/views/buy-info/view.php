<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use frontend\modules\dl\models\BuyInfo;

/* @var $this yii\web\View */
/* @var $model common\models\buyinfo */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Buyinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="buyinfo-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//             'id',
            'phone',
            'name',
//             'province_id',
                [
                        'label' => '所在省',
                        'value' => function ($model)
                        {
                            $buyInfo = new BuyInfo();
                            return $buyInfo->getAreaInfo($model->province_id)->area_name;
    }
    ],
//             'city_id',
            [
                    'label' => '所在市',
                    'value' => function ($model)
                    {
                        $buyInfo = new BuyInfo();
                        return $buyInfo->getAreaInfo($model->city_id)->area_name;
    }
    ],
//             'area_id',
//     [
//             'label' => '所在区',
//             'value' => function ($model)
//             {
//                 $buyInfo = new BuyInfo();
//                 return $buyInfo->getAreaInfo($model->area_id)->area_name;
//     }
//     ],
            'area_info',
//             'logistics_route_id',
//             'terminus_id',
            [
                'label' => '物流线路',
                'value' => function ($model)
                {
                    $buyInfo = new BuyInfo();
                    return $buyInfo->getLogisticsRoute($model->logistics_route_id)->logistics_route_name;
                }
            ],
//             [
//                 'label' => '落地点',
//                 'value' => function ($model)
//                 {
//                     $buyInfo = new BuyInfo();
//                     return $buyInfo->getTerminusName($model->terminus_id)->terminus_name;
//                 }
//             ],
//             'is_receive',
            [
                'label'=>'是否黑名单',
                 'value' => function ($model)
                 {
                    return $model->is_receive==1?'是':'否';
                  }
            ]
        ],
    ]) ?>

</div>
