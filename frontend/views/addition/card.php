<?php
/**********************版本一***************************/
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use frontend\assets\AdditionAddAsset;
AdditionAddAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\LogisticsReturnOrder */

$this->title = '添加车/司机';
$this->params['breadcrumbs'][] = ['label' => '添加线路', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;

?>
<p><?= Html::a('查询所有线路', ['index'], ['class' => 'btn btn-success']); ?></p>
<div class="addition-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-10">
            <div class="form-group">

                <?php $form = ActiveForm::begin([
                    'action' => ['addition/car-driver'],
                    'method'=>'post',
                    'options'=>['class'=>'form form-horizontal'],
                    'fieldConfig'=>[
                        'template'=> "{label}\n<div class='formControls col-md-5'>{input}</div>\n{error}",
                        'labelOptions'=>['class'=>'form-label col-md-2'],
                    ]
                ]); ?>
                <?php echo Html::input('hidden', 'LogisticsCar[logistics_route_id]', "$logistics_route_id", ['id' => 'logistics_route_id']) ?>

                <?php echo $form->field($modelRoute, 'logistics_route_name')->label('线路名') ?>

                <?php echo $form->field($modelCar, 'car_type_id')->dropDownList([1 => '是', 2 => '否'], ['prompt'=>'请选择'])->label('是否同城:') ?>
                <?php echo $form->field($modelCar, 'car_number')->label('车牌号:') ?>

                <?php echo $form->field($signupForm, 'username')->textInput()->label('司机用户名:'); ?>
                <?php if(!isset($type) || $type !== 'modification'){ ?>
                    <?php echo $form->field($signupForm, 'password')->passwordInput()->label('密码:'); ?>
                <?php } ?>
                <?php echo $form->field($signupForm, 'email')->label('邮箱:');?>
                <?php echo $form->field($signupForm, 'member_phone')->label('手机号:'); ?>
                <?php echo $form->field($signupForm, 'user_truename')->label('真实姓名:');?>
                <?php
                echo $form->field($signupForm, 'district')->label('省/市/区:')->widget(\chenkby\region\Region::className(),[
                    'model'=>$signupForm,
                    'url'=>\yii\helpers\Url::toRoute(['area/get-region']),
                    'province'=>[
                        'attribute'=>'member_provinceid',
                        'items'=>$area::getRegion(),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择省份']
                    ],
                    'city'=>[
                        'attribute'=>'member_cityid',
                        'items'=>$area::getRegion($signupForm['member_provinceid']),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择城市']
                    ],
                    'district'=>[
                        'attribute'=>'member_areaid',
                        'items'=>$area::getRegion($signupForm['member_cityid']),
                        'options'=>['class'=>'form-control form-control-inline sign-up-droplist','prompt'=>'选择县/区']
                    ]
                ]);
                ?>

                <div class="form-group">
                    <?= Html::submitButton('新增', ['class' => 'btn btn-primary']); ?>
                    <?= Html::a('重置', ['car-driver'], ['class' => 'btn btn-default']); ?>

                </div>

                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>

</div>
