<?php

use yii\helpers\Html;
use frontend\assets\AdditionAsset;

AdditionAsset::register($this);
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\LogisticsRoute */

$this->title = '添加司机:';
//$this->params['breadcrumbs'][] = ['label' => 'Logistics Routes', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->logistics_route_id, 'url' => ['view', 'id' => $model->logistics_route_id]];
//$this->params['breadcrumbs'][] = 'Update';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>

<div class="logistics-route-driverss">

    <p>
        <?= Html::a('添加线路', ['add-route-one'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('查询所有线路', ['index'], ['class' => 'btn btn-success']); ?>
    </p>

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-8">
                <table id="w0" class="table table-striped table-bordered detail-view">
                    <tbody>
                    <tr>
                        <th style="width:50px">线路名称:  <?= $model->logistics_route_name ?></th>
                    </tr>

                    </tbody>
                </table>


            <div class="row">
                <div class="col-sm-5">
                        <?php $form = ActiveForm::begin([
                            'action' => ['addition/add-drivers'],'method'=>'post',
                            'options'=>['id'=>'addDriver'],
                        ]); ?>

                        <?php echo Html::input('hidden','stype', '', ['id' => 'stype']) ?>
                        <?php echo Html::input('hidden','logistics_route_id', $model->logistics_route_id) ?>
                    <?php echo Html::dropDownList('username', null, $infoNotExist ,
                        ['class' => 'form-control', 'options' => ['class' => 'uVal'], 'id' => 'have-username','multiple' => 'multiple', 'size' => '20px']) ?>

                    <?php ActiveForm::end() ?>
                </div>
                <div class="col-sm-1">
                    <br><br>
                    <a class="btn btn-success btn-assign" onclick="handleDriver('add')" title="添加" data-target="available">&gt&gt;
                        <i class="glyphicon glyphicon-refresh glyphicon-refresh-animate" style="display: none;"></i></a><br><br>
                    <a class="btn btn-danger btn-assign" onclick="handleDriver('remove')" title="移除" data-target="assigned">&lt&lt; <i
                                class="glyphicon glyphicon-refresh glyphicon-refresh-animate" style="display: none;"></i></a></div>
                <div class="col-sm-6">
                        <?php $form = ActiveForm::begin([
                            'action' => ['addition/add-drivers'],'method'=>'post',
                            'options'=>['id'=>'removeDriver'],
                        ]); ?>
                    <?php echo Html::input('hidden','stype', '', ['id' => 'rtype']) ?>
                    <?php echo Html::input('hidden','logistics_route_id', $model->logistics_route_id) ?>
                    <?php echo Html::dropDownList('username', null, $infoExist , ['class' => 'form-control', 'id' => 'have-username', 'multiple' => 'multiple', 'size' => '10px']) ?>
                    <?php ActiveForm::end() ?>
                </div>
            </div>

        </div>
    </div>

</div>