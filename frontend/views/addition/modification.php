<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Area;
use yii\widgets\ActiveForm;
use frontend\assets\AdditionAsset;
AdditionAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\RouteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '所有线路';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;

?>
<div class="logistics-route-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加线路', ['add-route'], ['class' => 'btn btn-success']) ?>

        <?php echo Html::a('老版本', ['index'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'logistics_route_name',
                'label' => '线路名',
            ],
            [
                'label' => '所属省',
                'attribute' => 'province_id',
                'value' => function ($model) {
                    $Areainfo = (new Area())->getAreaInfo(['area_id' => $model->routeOfLogisticsArea->province_id]);
                    return isset($Areainfo->area_name) ? $Areainfo->area_name : '';

                }
            ],
            [
                'label' => '所属市',
                'attribute' => 'city_id',
                'value' => function ($model) {
                    $Areainfo = (new Area())->getAreaInfo(['area_id' => $model->routeOfLogisticsArea->city_id]);
                    return isset($Areainfo->area_name) ? $Areainfo->area_name : '';
                }
            ],
            [
                'label' => '所属区/县',
                'attribute' => 'area_id',
                'value' => function ($model) {
                    $Areainfo = (new Area())->getAreaInfo(['area_id' => $model->routeOfLogisticsArea->area_id]);
                    return isset($Areainfo->area_name) ? $Areainfo->area_name : '';
                }
            ],
            [
                'label' => '拼音',
                'attribute' => 'pinyin_name',
                'contentOptions' => ['id' => 'pinyin_id'],
                'value' => function($model){
                    if($model->routeOfLogisticsArea->area_id){
                        $Areainfo = (new Area())->getAreaInfo(['area_id' => $model->routeOfLogisticsArea->area_id]);
                    }
                    else if($model->routeOfLogisticsArea->city_id){
                        $Areainfo = (new Area())->getAreaInfo(['area_id' => $model->routeOfLogisticsArea->city_id]);
                    }
                    return isset($Areainfo->pinyin_name) ? $Areainfo->pinyin_name : '';
                }
            ],


            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {driver}',
//                'template' => '{update} {view}',
                'buttons' => [
                    'view' => function($url,$model,$key){
                        $url = 'javascript:void(0)';
                        $options=[
                            'title'=>Yii::t('yii', '是否显示'),
                            'id' => 'disPinyin',
                            'rel' => $model->logistics_route_id,
                        ];

                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',$url ,$options);
                    },
                    'driver' => function($url, $model, $key){
                        $options=[
                            'title'=>Yii::t('yii', '添加司机'),
                        ];

                        return Html::a('<span class="glyphicon glyphicon-plus"></span>',$url ,$options);
                    }
                ],

                'contentOptions' => ['width' => '30px'],
            ],
        ],
    ]); ?>
</div>

<!-- 模态框 -->
<div class="modal fade" id="Modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel jq_message">请输入:</h4>
            </div>
            <div class="modal-body">
                <form method="post" action="index.php?r=addition/view" class="form-horizontal" role="form">
                    <div class="form-group">
                        <label for="inputname" class="col-sm-2 control-label">pingyin_name:</label>
                        <div class="col-sm-8">
                            <input type="text" name="pinyin_name" id="inputname" class="form-control">
                            <input type="hidden" name="logistics_route_id" id="logistics_route_id" value="" class="form-control">
                            <input name="_csrf-frontend" type="hidden" id="_csrf-frontend" value="<?= Yii::$app->request->csrfToken ?>">
                        </div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">提交</button>
            </div>
            </form>

        </div>
    </div>
</div>
