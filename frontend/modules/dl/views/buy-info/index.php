<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Area;

/* @var $this yii\web\View */
/* @var $searchModel common\models\buyinfoSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '收货人管理';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
<div class="buyinfo-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<!--     <p> -->
        <?php //echo Html::a('添加收货人', ['create'], ['class' => 'btn btn-success']) ?>
<!--     </p> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
//             'id',
            'phone',
            'name',
//             'province_id',
                [
                        'label'=>'所在省',
//                         'attribute' =>'is_receive',
                        'value'=>function($model)
                        {
                            return $model->getAreaInfo($model->province_id)->area_name;
    },
    ],
//             'city_id',
            [
                    'label'=>'所在市',
                    'attribute' =>'city_id',
                    'value'=>function($model)
                    {
                        return $model->getAreaInfo($model->city_id)->area_name;
    },
    'filter'=>Area::getRegion(6),
    ],
            // 'area_id',
            // 'area_info',
            // 'logistics_route_id',
            // 'terminus_id',
//             'is_receive',
            [
                'label'=>'是否加入黑名单',
                    'attribute' =>'is_receive',
                    'value'=>function($model)
                    {
                        return $model->is_receive == 0?'否':'是';
        },
                    'filter'=>['0'=>'否','1'=>'是'],
    ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
