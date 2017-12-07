<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\buyinfo */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Buyinfos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
                            $res = $model->getAreaInfo($model->province_id);
                            if(!empty($res))
                            {
                                return $res->area_name;
                            }else {
                                return '';
                            }
//                             return $model->getAreaInfo($model->province_id)->area_name;
    }
    ],
//             'city_id',
            [
                    'label' => '所在市',
                    'value' => function ($model)
                    {
                        $res = $model->getAreaInfo($model->city_id);
                        if(!empty($res))
                        {
                            return $res->area_name;
                        }else {
                            return '';
                        }
//                         return $model->getAreaInfo($model->city_id)->area_name;
    }
    ],
//             'area_id',
    [
            'label' => '所在区',
            'value' => function ($model)
            {
                $res = $model->getAreaInfo($model->area_id);
                if(!empty($res))
                {
                    return $res->area_name;
                }else {
                    return '';
                }
//                 return $model->getAreaInfo($model->area_id)->area_name;
    }
    ],
            'area_info',
//             'logistics_route_id',
//             'terminus_id',
            [
                'label' => '物流线路',
                'value' => function ($model)
                {
                    $res = $model->getLogisticsRoute($model->logistics_route_id);
                    if(!empty($res))
                    {
                        return $res->logistics_route_name;
                    }else {
                        return '';
                    }
                }
            ],
            [
                'label' => '落地点',
                'value' => function ($model)
                {
                    $res = $model->getTerminusName($model->terminus_id);
                    if(!empty($res))
                    {
                        return $res->terminus_name;
                    }else {
                        return '';
                    }
//                     return $model->getTerminusName($model->terminus_id)->terminus_name;
                }
            ],
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
