<?php

use yii\helpers\Html;
use yii\grid\GridView;
use frontend\modules\dl\models\User;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use frontend\assets\SmallPrintAsset;
SmallPrintAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel common\models\SmallPrintSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '小码单打印记录';
$this->params['breadcrumbs'][] = $this->title;
$this->params['leftmenus'] = $menus;
?>
 <h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(['method'=>'get'])?>
<?= $form->field($searchModel, 'print_time')->label('打印时间查询')->widget(DateRangePicker::classname(), [
            'convertFormat'=>true,
            'presetDropdown'=>true,
            'model'=>$searchModel,
            'options' => [
                'class' => 'form-control',
                'value' => $print_time ? $print_time['date'] : date('Y-m-d') .' - ' . date('Y-m-d') ,
            ],
            'pluginOptions'=>[
                'timePicker'=>false,
                'timePickerIncrement'=>5,
                'locale'=>['format'=>'Y-m-d', 'separator'=>' - ',]
            ]
   ])?>
   <?php echo Html::submitButton('搜索', ['class'=>'btn btn-primary','name' =>'submit-button']) ?>
<?php ActiveForm::end()?>
<div class="small-print-index">

   
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => 'print_time',
                'label'=> '打印时间',
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->print_time);
                }
            ],
            [
                'attribute' => 'print_member_id',
                'label'=> '操作人',
                'value' => function($model){
                    return User::findOne($model->print_member_id)->user_truename;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                    $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('查看', $url, $options);
                    }
                ]
            ],
        ],
    ]); ?>
</div>
