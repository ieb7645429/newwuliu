<?php
/*******************版本一************************/
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
                'action' => ['addition/add-route-one'],
                'method'=>'post',
                'options'=>['class'=>'form form-horizontal'],
                //    'id'=>'form-dict-add',
                'fieldConfig'=>[
                    //        'template'=> "{label}\n<div class='formControls col-xs-3 col-sm-4'>{input}</div>\n{error}",
                    'template'=> "{label}\n<div class='formControls col-md-5'>{input}</div>\n{error}",
                    'labelOptions'=>['class'=>'form-label col-md-2'],
                    //    'options'=>['class'=>'row cl'],
                ]
            ]); ?>
            <input type="hidden" id="subType" name="subType" value="<?= Html::encode(isset($type) ? $type: ''); ?>">
            <?php echo $form->field($model, 'logistics_route_name')->label('路线名称:');?>
            <?php echo $form->field($model, 'logistics_route_code')->label('地区字母编号(SY):');?>
            <?php echo $form->field($model, 'logistics_route_no')->label('地区数字编号（001）:');?>
            <?php echo $form->field($model, 'same_city')->dropdownList([1 => '是', 2 => '否',], ['prompt'=>'请选择'])->label('是否同城:');?>


            <?php
            echo $form->field($modelLogisticsArea, 'district')->label('省/市/区:')->widget(\chenkby\region\Region::className(),[
                'model'=>$modelLogisticsArea,
                'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                'province'=>[
                    'attribute'=>'province_id',
                    'items'=>$area::getRegion(),
                    'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份']
                ],
                'city'=>[
                    'attribute'=>'city_id',
                    'items'=>$area::getRegion($modelLogisticsArea['province_id']),
                    'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市']
                ],
                'district'=>[
                    'attribute'=>'area_id',
                    'items'=>$area::getRegion($modelLogisticsArea['city_id']),
                    'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区']
                ]
            ]);
            ?>

            <div class="form-group field-logisticsroute-pinyin_name">
                <label class="form-label col-md-2" for="pinyin_name">拼音添加:</label>
                <div class="formControls col-md-5">
                    <?=Html::input('text','Area[pinyin_name]',$area->pinyin_name,['id' => "logisticsroute-pinyin_name" ,'class'=>'form-control col-md-5','placeholder'=>'']);?>
                </div>
                <div class="help-block" id="help-block-pinyin_name"></div>
            </div>


            <div class="form-group">
                <?= Html::submitButton('新增', ['class' => 'btn btn-primary', 'id' => 'creation', 'onclick' => "return setSubType('creation')"]) ?>
                <?= Html::a('重置', ['add-route-one'], ['class' => 'btn btn-default']) ?>

            </div>

            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>




