<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Area;
use common\models\ShippingTpye;

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-md-10">
        <div class="form-group">

            <?php $form = ActiveForm::begin([
                'action' => ['addition/update-route'],
                'method'=>'post',
                'options'=>['class'=>'form form-horizontal'],
                'fieldConfig'=>[
                    'template'=> "{label}\n<div class='formControls col-md-5'>{input}</div>\n{error}",
                    'labelOptions'=>['class'=>'form-label col-md-2'],
                ]
            ]); ?>
<!--            <input type="hidden" id="subType" name="subType" value="--><?//= Html::encode(isset($type) ? $type: ''); ?><!--">-->
            <input type="hidden" id="subType" name="subType" value="">
            <input type="hidden" id="logistics_route_id" name="LogisticsRoute[logistics_route_id]" value="<?= Html::encode(isset($id) ? $id: ''); ?>">
            <?php echo $form->field($model, 'logistics_route_name')->label('路线名称:');?>
            <?php echo $form->field($model, 'logistics_route_code')->label('地区字母编号(SY):');?>
            <?php echo $form->field($model, 'logistics_route_no')->label('地区数字编号（001）:');?>
            <?php echo $form->field($model, 'same_city')->dropdownList([1 => '是', 2 => '否',], ['prompt'=>'请选择'])->label('是否同城:');?>
            <?php
            echo $form->field($logiscticAreaInfo, 'district')->label('省/市/区:')->widget(\chenkby\region\Region::className(),[
                'model'=>$logiscticAreaInfo,
                'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                'province'=>[
                    'attribute'=>'member_provinceid',
                    'items'=>$area::getRegion(),
                    'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份']
                ],
                'city'=>[
                    'attribute'=>'member_cityid',
                    'items'=>$area::getRegion($logiscticAreaInfo['member_provinceid']),
                    'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市']
                ],
                'district'=>[
                    'attribute'=>'member_areaid',
                    'items'=>$area::getRegion($logiscticAreaInfo['member_cityid']),
                    'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区']
                ]
            ]);
            ?>

            <?php echo $form->field($model_driver, 'driver_id')->dropDownList($driversRes, ['prompt' => '选择司机'])->label('司机:'); ?>


            <!--            先显示所以司机, 再显示所有车-->
            <?php //echo $form->field($modelCar, 'logistics_car_id')->label('车牌:')->dropDownList($carInfo, ['prompt' => '请选择']); ?>
            <?php echo $form->field($modelCar, 'car_number[]')->textInput(['id' => 'logistics_car_number'])->label('车牌号:'); ?>

            <?php echo $form->field($modelCar, 'car_number[]')->textInput()->label('修改车牌号:'); ?>


<!--            <div class="form-group field-logisticscar-car_number required">-->
<!--                <label class="form-label col-md-2" for="logisticscar-car_number">修改车牌号:</label>-->
<!--                <div class="formControls col-md-5">-->
<!--                    --><?php //echo Html::input('text', 'LogisticsCar[car_number]', '', ['class' => "form-control"]) ?>
<!--                </div>-->
<!--                <div class="help-block"></div>-->
<!--            </div>-->


            <div class="form-group field-logisticsroute-pinyin_name">
                <label class="form-label col-md-2" for="pinyin_name">拼音添加<span style="color:red; font-size: 11px">(清空拼音后,不显示)</span>:</label>
                <div class="formControls col-md-5">
                    <?=Html::input('text','Area[pinyin_name]',$area->pinyin_name,['id' => "logisticsroute-pinyin_name" ,'class'=>'form-control col-md-5','placeholder'=>'']);?>
                </div>
                <div class="help-block" id="help-block-pinyin_name"></div>
            </div>

            <div class="form-group">
                    <?= Html::submitButton('修改', ['class' => 'btn btn-info', 'id' => 'modification', 'onclick' => "return setSubType('modification')"]) ?>
                    <?= Html::submitButton('删除车/司机', ['class' => 'btn btn-primary', 'id' => 'delCar', 'onclick' => "return setSubType('delCarDriver')"]) ?>
                    <?php //echo Html::submitButton('删除司机', ['class' => 'btn btn-primary', 'id' => 'delDriver', 'onclick' => "return setSubType('delDriver')"]) ?>
                    <?= Html::button('重置', ['class' => 'btn btn-default', 'onclick' => 'javascript:window.location.reload()']) ?>
            </div>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>




